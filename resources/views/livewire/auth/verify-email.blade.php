<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Log;

new #[Layout('components.layouts.auth')] class extends Component {
    public $otpDigits = ['', '', '', '', '', ''];
    public $otpSent = false;
    public $timer = 300;
    public $canResend = false;

    public function mount()
    {
        $user = Auth::user();

        if ($user && $user->email_otp_expires_at && now()->lessThan($user->email_otp_expires_at)) {
            // OTP active — restore remaining time
            $this->timer = now()->diffInSeconds($user->email_otp_expires_at);
            $this->otpSent = true;
            $this->canResend = false;
        } else {
            // No active OTP — send new one
            $this->sendVerification();
        }
    }

    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        if ($user->email_otp && $user->email_otp_expires_at && now()->lessThan($user->email_otp_expires_at)) {
            Log::info('OTP already active, not resending.');
            $remaining = now()->diffInSeconds($user->email_otp_expires_at);
            Log::info("OTP for user {$user->id} Remaining time for OTP: {$remaining} seconds");
            $this->otpSent = true;
            $this->canResend = false;
            $this->timer = $remaining;
            Session::flash('status', 'otp-already-active');
            return;
        }

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $otp = '';
        for ($i = 0; $i < 6; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }

        Log::info("Generated OTP for user {$user->id}: {$otp}");
        if ($user && $otp) {
            Log::info("User email: {$user->email}");
            $success = $user->update([
                'email_otp' => $otp,
                'email_otp_expires_at' => now()->addSeconds($this->timer),
            ]);
            $user->save();

            if ($success) {
                Log::info("Successfully updated OTP for user {$user->id}");
            } else {
                Log::error("Failed to update OTP for user {$user->id}");
            }
        }

        Mail::raw("Your verification code is: {$otp}", function ($message) use ($user) {
            $message->to($user->email)->subject('Your Email Verification Code');
        });

        $this->otpSent = true;
        $this->canResend = false;
        $this->timer = $this->timer; // Reset timer to 5 minutes

        Session::flash('status', 'otp-sent');
    }

    public function verifyOtp()
    {
        $user = Auth::user();
        $otp = implode('', $this->otpDigits);

        if (!$otp) {
            Session::flash('status', 'no-otp');
            return;
        }

        if ($user->email_otp === $otp && now()->lessThan($user->email_otp_expires_at)) {
            $user->update([
                'email_verified_at' => now(),
                'email_otp' => null,
                'email_otp_expires_at' => null,
            ]);

            Session::flash('status', 'verified');
            $this->redirect(route('dashboard', absolute: false), navigate: true);
        } else {
            Session::flash('status', 'invalid-otp');
        }
    }

    public function enableResend()
    {
        $this->canResend = true;
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function login()
    {
        $user = Auth::user();
        if ($user && $user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }
        $this->redirect(route('login', absolute: false), navigate: true);
    }
}; ?>


<div class="mt-4 flex flex-col gap-6">
    <h2 class="text-xl font-semibold mb-2">OTP Input</h2>
    <p class="text-gray-500 mb-6">Enter your one-time password</p>

    @php
        $user = Auth::user();
        $otpActive =
            $user && $user->email_otp && $user->email_otp_expires_at && now()->lessThan($user->email_otp_expires_at);
    @endphp

    <p class="{{ $otpActive ? 'text-green-500' : 'text-gray-500' }}">
        {{ $otpActive ? 'You have an active OTP code.' : 'No active OTP code.' }}
    </p>

    @php
        $userData = $user
            ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified' => (bool) $user->email_verified_at,
                'email_otp' => $user->email_otp,
                'otp_active' => $otpActive,
                'email_otp_expires_at' => $user->email_otp_expires_at
                    ? $user->email_otp_expires_at->toDateTimeString()
                    : null,
                'created_at' => $user->created_at->toDateTimeString(),
                'updated_at' => $user->updated_at->toDateTimeString(),
            ]
            : null;
        $userJson = json_encode($userData, JSON_PRETTY_PRINT);
    @endphp

    <div class="bg-gray-100 p-4 rounded-lg mb-6">
        <h3 class="text-sm font-semibold mb-2">User Data (JSON):</h3>
        <pre class="text-xs overflow-auto max-h-48 bg-gray-50 p-2 rounded border border-gray-200">{{ $userJson }}</pre>
    </div>

    @if (session('status') == 'otp-sent')
        <p class="text-green-600 text-sm mb-3">OTP has been sent to your email.</p>
    @elseif (session('status') == 'invalid-otp')
        <p class="text-red-500 text-sm mb-3">Invalid or expired OTP.</p>
    @elseif (session('status') == 'verified')
        <p class="text-green-500 text-sm mb-3">Email verified successfully!</p>
    @endif

    <div class="flex justify-center gap-2 mb-6">
        @foreach (range(0, 5) as $i)
            <input type="text" maxlength="1" wire:model="otpDigits.{{ $i }}"
                class="w-10 h-12 text-center text-lg border border-gray-300 rounded-md focus:border-green-500 focus:ring-1 focus:ring-green-500"
                oninput="this.value = this.value.toUpperCase(); if(this.value.length===1 && this.nextElementSibling){this.nextElementSibling.focus()}"
                onkeydown="if(event.key==='Backspace' && !this.value && this.previousElementSibling){this.previousElementSibling.focus()}" />
        @endforeach
    </div>

    <button wire:click="verifyOtp" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
        Submit
    </button>

    <!-- ✅ Fixed AlpineJS Timer + Resend Logic -->
    <div x-data="{
        expiry: new Date('{{ $user && $user->email_otp_expires_at ? $user->email_otp_expires_at->toIso8601String() : now()->addSeconds($timer)->toIso8601String() }}').getTime(),
        remaining: 0,
        canResend: false,
        interval: null,
        init() {
            this.updateRemaining();
            this.interval = setInterval(() => this.updateRemaining(), 1000);
        },
        updateRemaining() {
            const diff = Math.max(0, this.expiry - Date.now());
            this.remaining = Math.floor(diff / 1000);
            if (this.remaining <= 0) {
                clearInterval(this.interval);
                this.canResend = true;
                $wire.enableResend();
            }
        },
        formattedTime() {
            const m = String(Math.floor(this.remaining / 60)).padStart(2, '0');
            const s = String(this.remaining % 60).padStart(2, '0');
            return `${m}:${s}`;
        }
    }" x-init="init()" class="mt-6 text-sm text-gray-500 text-center">
        <template x-if="!canResend">
            <p>Resend available in <span x-text="formattedTime()"></span></p>
        </template>

        <button x-show="canResend"
            @click="$wire.sendVerification(); canResend = false; expiry = Date.now() + 60000; updateRemaining();"
            class="mt-2 px-4 py-2 rounded-lg border border-green-500 text-green-500 hover:bg-green-50">
            Resend OTP
        </button>

        <flux:link class="text-sm mt-4 cursor-pointer text-gray-500" wire:click="logout">
            Log out
        </flux:link>
    </div>
</div>


<script>
    function otpTimer(seconds, onExpire) {
        return {
            total: seconds,
            remaining: seconds,
            formatted: '05:00',
            canResend: false,
            interval: null,

            init() {
                this.updateFormatted();
                this.start();
            },
            start() {
                this.interval = setInterval(() => {
                    if (this.remaining <= 0) {
                        clearInterval(this.interval);
                        this.canResend = true;
                        if (typeof onExpire === 'function') onExpire();
                        return;
                    }
                    this.remaining--;
                    this.updateFormatted();
                }, 1000);
            },
            updateFormatted() {
                const m = String(Math.floor(this.remaining / 60)).padStart(2, '0');
                const s = String(this.remaining % 60).padStart(2, '0');
                this.formatted = `${m}:${s}`;
            },
            restart(seconds) {
                this.remaining = seconds;
                this.canResend = false;
                this.updateFormatted();
                this.start();
            }
        }
    }
</script>
