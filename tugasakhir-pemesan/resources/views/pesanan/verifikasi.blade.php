@extends('layout.sneat')

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Verification</li>
    </ol>
@endsection

@section('menu')
<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <h5 class="font-weight-bold">File Verification</h5>
            
            <!-- File Preview Section -->
            <div class="file-preview mb-4">
                <iframe src="path/to/your/file.pdf" width="100%" height="400" style="border: none;"></iframe>
                <div class="text-right mt-2">
                    <button class="btn btn-secondary" id="fullPreviewBtn">Show Full Preview</button>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#confirmationModal">Confirm Verification</button>
                </div>
            </div>

            <!-- Text Area for Changes -->
            <div class="form-group">
                <label for="changeRequest">Ajukan Perubahan</label>
                <textarea class="form-control" id="changeRequest" rows="3" placeholder="Enter your changes here..."></textarea>
                <button class="btn btn-success mt-2" id="submitChangeRequest">Submit Changes</button>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Confirm Verification</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to confirm the verification of this file?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmVerification">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
 $(document).ready(function() {
    $('#fullPreviewBtn').on('click', function() {
        window.open('path/to/your/file.pdf', '_blank');
    });

    $('#confirmVerification').on('click', function() {
        alert('Verification confirmed!');
        $('#confirmationModal').modal('hide');
    });

    $('#submitChangeRequest').on('click', function() {
        const changes = $('#changeRequest').val();
        if (changes) {
            // Send changes to the server
            alert('Change request submitted: \n' + changes);
            $('#changeRequest').val(''); // Clear the textarea
        } else {
            alert('Please enter your changes before submitting.');
        }
    });
});
</script>
@endsection

@endsection