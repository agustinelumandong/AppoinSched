<div>
    <div class="flux-card mb-4" style="padding: 12px;">
        <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
            <h5 class="mb-0 fw-semibold">Document Requests</h5>
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="flux-form-control search" placeholder="Search document requests"
                        wire:model.live="search" wire:keyup="searchs" wire:debounce.300ms>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table flux-table mb-0">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Requestor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Document For</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Office</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentRequests as $documentRequest)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $documentRequest->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $documentRequest->user?->first_name . ' ' . $documentRequest->user?->last_name ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $documentRequest->user?->email ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    @if ($documentRequest->details)
                                        {{ $documentRequest->details->first_name }}
                                        {{ $documentRequest->details->last_name }}
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                                                                                                {{ $documentRequest->details->request_for === 'myself' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $documentRequest->details->request_for)) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">No details</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="fw-semibold">{{ $documentRequest->service?->title ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="fw-semibold">{{ $documentRequest->office?->name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="flux-badge flux-badge-{{ match ($documentRequest->status) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'completed' => 'success',
                                        default => 'light',
                                    } }}">
                                    {{ ucfirst($documentRequest->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="fw-semibold">
                                    {{ $documentRequest->details?->created_at->format('M d, Y') ?? 'N/A' }}
                                </p>
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.view-document-request', Crypt::encryptString($documentRequest->id)) }}"
                                        wire:navigate class="flux-btn flux-btn-primary btn-sm" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-badge display-4 mb-3"></i>
                                    <div>No document requests found. Create your first document request to get started.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documentRequests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $request->id }}
                            </td>

                            <!-- Requestor column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-800">
                                                {{ substr($request->user->first_name, 0, 1) }}{{ substr($request->user->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $request->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $request->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- NEW: Document For column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($request->details)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $request->details->first_name }} {{ $request->details->last_name }}
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                                                                                    {{ $request->details->request_for === 'myself' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $request->details->request_for === 'myself' ? 'Self' : 'Someone-Else' }}
                                        </span>

                                        @if ($request->details->request_for === 'someone_else')
                                            <svg class="ml-1 w-3 h-3 text-amber-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 italic">
                                        {{ $request->user->name }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        Legacy request
                                    </div>
                                @endif
                            </td>

                            <!-- Service column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $request->service->title }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $request->service->description ?? 'No description' }}
                                </div>
                            </td>

                            <!-- Office column -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $request->office->name }}
                            </td>

                            <!-- Status column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="flux-badge flux-badge-{{ $request->status === 'pending' ? 'warning text-amber-800' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>

                            <!-- Date column -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->format('M d, Y') }}
                                <div class="text-xs text-gray-400">
                                    {{ $request->created_at->format('g:i A') }}
                                </div>
                            </td>

                            <!-- Actions column -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.view-document-request', Crypt::encryptString($documentRequest->id)) }}"
                                    wire:navigate
                                    class="flux-btn flux-btn-outline btn-sm text-blue-600 hover:text-blue-900 font-medium"
                                    title="View Details">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium">No document requests found</p>
                                    <p class="text-sm">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody> --}}
            </table>
        </div>
        <!-- Pagination links -->
        <div class="mt-3">
            {{ $documentRequests->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

</div>
