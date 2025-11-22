<div class="w-full md:w-1/2">
    <div class="flux-card p-6 mb-6">
        <h4 class="text-lg font-bold mb-4">{{ label_with_bisaya('Groom Information', 'groom_information') }}</h4>
        @include(
            'livewire.documentrequest.components.document-request-steps.document-request-forms.partials.person-info',
            [
                'prefix' => 'groom',
                'fields' => [
                    'first_name',
                    'middle_name',
                    'last_name',
                    'suffix',
                    'age',
                    'date_of_birth',
                    'place_of_birth',
                    'sex',
                    'citizenship',
                    'residence',
                    'religion',
                    'civil_status',
                ],
            ]
        )
        <h5 class="text-md font-bold mt-6 mb-2">{{ label_with_bisaya('Parental Information', 'parental_information') }}</h5>
        @include(
            'livewire.documentrequest.components.document-request-steps.document-request-forms.partials.parent-info',
            [
                'prefix' => 'groom_father',
                'label' => label_with_bisaya('Father', 'father'),
            ]
        )
        @include(
            'livewire.documentrequest.components.document-request-steps.document-request-forms.partials.parent-info',
            [
                'prefix' => 'groom_mother',
                'label' => label_with_bisaya('Mother', 'mother'),
                'maiden' => true,
            ]
        )
    </div>
</div>
