@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Add New Alizeti Record</h4>

            <form action="{{ route('alizeti.store') }}" method="POST" id="alizetiForm">
                @csrf

                <div class="form-group">
                    <label for="tarehe">Select Date</label>
                    <input type="date" id="tarehe" name="tarehe" class="form-control datepicker" placeholder="select date">
                </div>

                <div class="form-group">
                    <label for="al_kilogram">Al Kilogram</label>
                    <input type="number" class="form-control" id="al_kilogram" name="al_kilogram">
                </div>

                <div class="form-group">
                    <label for="gunia_total">Total Gunia</label>
                    <input type="number" class="form-control" id="gunia_total" name="gunia_total">
                </div>

                <div class="form-group">
                    <label for="price_per_kilo">Price per Kilo</label>
                    <input type="number" class="form-control" id="price_per_kilo" name="price_per_kilo">
                </div>

                <div class="form-group">
                    <label for="batch_no_display">Batch No</label>
                    <input type="text" class="form-control" id="batch_no_display" readonly>
                    <input type="hidden" name="batch_no" id="batch_no">
                </div>

                <button type="button" class="btn btn-primary mt-3" id="generateAndConfirm"><i class="fas fa-save"></i>Submit</button>
                <a href="{{ route('alizeti.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i>Back</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('alizetiForm');
        const batchNoDisplay = document.getElementById('batch_no_display');
        const batchNoInput = document.getElementById('batch_no');
        const generateAndConfirmButton = document.getElementById('generateAndConfirm');

        generateAndConfirmButton.addEventListener('click', function() {
            const formData = new FormData(form);
            const tarehe = formData.get('tarehe');
            const alKilogram = formData.get('al_kilogram');
            const guniaTotal = formData.get('gunia_total');
            const pricePerKilo = formData.get('price_per_kilo');

            if (!tarehe || !alKilogram || !guniaTotal || !pricePerKilo) {
                Swal.fire('Error', 'Please fill in all required fields.', 'error');
                return;
            }

            fetch('{{ route('alizeti.generate-batch') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(batchNo => {
                batchNoDisplay.value = batchNo;
                batchNoInput.value = batchNo;

                Swal.fire({
                    title: 'Confirm Submission',
                    text: `Batch No: ${batchNo}. Are you sure you want to submit?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An error occurred.', 'error');
            });
        });
    });
</script>
@endsection