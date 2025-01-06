@extends('layout.sneat')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Vendors</a></li>
    <li class="breadcrumb-item" aria-current="page">Layanan</li>
    <li class="breadcrumb-item active" aria-current="page">Detail Layanan</li>
  </ol>
@endsection
@section("menu")

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="form-group">
                        <label for="editInput">Detail</label>
                        <input type="text" class="form-control" id="editInput" required>
                    </div>
                    <input type="hidden" id="detailId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Penghapusan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus detail ini dan semua opsinya?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batalkan</button>
                <form id="deleteForm" action="{{ route('detail.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div id="hiddens">

                    </div>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<ul class="list-inline p-3">
    <li class="list-inline-item h2">{{ $layanan['namaLayanan'] }}</li>
    <li class="list-inline-item">
        <div class="rating-images">
          @for ($i = 0; $i < 5; $i++)
          @if ($i < round($layanan['rating']))  
              <img class="img-fluid" src="{{ asset('../assets/images/rating.png') }}" alt="">
          @else
          <img style="opacity: 0.5;" class="img-fluid" src="{{ asset('../assets/images/rating.png') }}" alt="">
          @endif
          
          @endfor
        </div>
    </li>
    <li class="list-inline-item h6">({{ $layanan['totalNota'] }})</li>
</ul>
<div class="form-check ">
    <label class="form-check-label px-4 py-2">
      <input type="checkbox" class="form-check-input" name="" id="" value="checkedValue" checked>
      Menerima Pesanan
    </label>
  </div>
  <a class="px-4" href="/detailpesanan/fotokopi/review">Lihat review</a>

  <div class="form-group px-4 py-2">
    <label for="">Deskripsi</label>
    <textarea class="form-control" name="" id="deskripsi" rows="3"></textarea>
  </div>
  <button class="button btn-primary mx-4" onclick="simpandeskripsi()">Simpan Deskripsi</button>
  <div class="form-group px-4 py-2 col-sm-5">
    <label for="pilihanjenisbahan">Pilihan dan Jenis Barang</label>
<span>
    <div class="select-container">
        <select class="form-control custom-select" name="pilihanjenisbahan" id="pilihanjenisbahan">
            @foreach($jenisbahan as $j)
                <option value="{{$j->idjenisbahan}}">{{$j->namajenisbahan}}</option>
            @endforeach
        </select>
        <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
    </div>
</span>
    <span>
        <div id="editHarga">
            <a href="/layanancetak/editharga/{{ $jenisbahan[0]->idjenisbahan }}" class="btn btn-primary">Edit Harga</a>
        </div>
        
    </span>
    <div class="py-2" id="jenis-bahan-actions">
        
        <a href="/vendors/{{ $jenisbahan[0]->idvendor }}/layanan/{{ $jenisbahan[0]->idlayanan }}/edit/{{ $jenisbahan[0]->idjenisbahan }}" 
          class="btn btn-primary">
          Edit Jenis Bahan
        </a>
    </div>
    
  </div>
  <div id="listOpsi">
    @foreach ($opsiDetail as $od)
    <div class="form-group px-4 py-2 col-sm-5">
      <label id="detail-{{ $od['detail']->id }}">{{ $od['detail']->value }}</label>
      <button type="button" class="btn btn-secondary btn-sm mx-2 edit-button" 
      data-id="{{ $od['detail']->id }}">Edit
    </button>
      <button type="button" class="btn btn-danger btn-sm mx-2 delete-button" 
        data-id="{{ $od['detail']->id }}" 
        data-vendor="{{ $jenisbahan[0]->idvendor }}" 
        data-layanan="{{ $jenisbahan[0]->idlayanan }}" 
        data-toggle="modal" 
        data-target="#confirmDeleteModal">Delete
        </button>
      <span>
          <div class="select-container">
            @if (isset($od['opsi'][0]['id']))
              <select class="form-control custom-select" name="pilihanopsi" id="pilihanopsi-{{$od['detail']->id}}">
                @foreach($od['opsi'] as $o)
                  <option value="{{ $o['id'] }}">{{ $o['opsi'] }}</option>
                @endforeach
              </select>
              <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
              @endif
            </div>
      </span>
      <div class="py-2">
        @if (isset($od['opsi'][0]['id']))
        <a id="editopsilink-{{$od['detail']->id}}" 
          href="/opsidetail/{{ $jenisbahan[0]->idvendor }}/{{ $jenisbahan[0]->idlayanan }}/{{ $od['opsi'][0]['id'] }}" 
          class="btn btn-primary">
          Edit Opsi
        </a>
        
        @endif
        <a id="addopsilink-{{$od['detail']->id}}" 
            href="/addopsidetail/{{ $jenisbahan[0]->idvendor }}/{{ $jenisbahan[0]->idlayanan }}/{{ $od['detail']->id }}"
            class="btn btn-primary">
            Tambah Opsi
          </a>
          @if (isset($od['opsi'][0]['id']))
          <a id="deleteopsilink-{{$od['detail']->id}}" 
            href="/deleteopsidetail/{{ $jenisbahan[0]->idvendor }}/{{ $jenisbahan[0]->idlayanan }}/{{ $od['opsi'][0]['id'] }}" 
            class="btn btn-danger">
            Delete Opsi
          </a>
          
        @endif
      </div>
      
    </div>
    @endforeach 
  </div>
  
  <div class="px-4 py-2 d-flex justify-content-center">
    <div id="tambahdetail">
        <a href="/createdetail/{{ $jenisbahan[0]->idvendor }}/{{ $jenisbahan[0]->idlayanan }}/{{ $jenisbahan[0]->idjenisbahan }}"  class="btn btn-primary">Tambah Detail</a>
    </div>
  </div>
  
  
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {

    let deleteDetailId;
    let deleteVendorId; 
    let deleteLayananId;

    $(document).on('click', '.delete-button', function() {
        deleteDetailId = $(this).data('id');
        deleteVendorId = $(this).data('vendor');
        deleteLayananId = $(this).data('layanan');

        $("#hiddens").html(
            `
            <input type="hidden" name="idvendor" value="${deleteVendorId}">
            <input type="hidden" name="idlayanan" value="${deleteLayananId}">
            <input type="hidden" name="iddetail" value="${deleteDetailId}">
            `
        );

        $('#confirmDeleteModal').modal('show');
    });

    $('[id^="pilihanopsi-"]').on('change', function() {
        const $selector = $(this); // The dropdown that triggered the change
        const selectedValue = $selector.val(); // Get the selected value
        const $formGroup = $selector.closest('.form-group'); // Find the closest form group

        // Update the edit link within this form group
        const $editLink = $formGroup.find('[id^="editopsilink-"]');
        if ($editLink.length) {
            const currentEditHref = $editLink.attr('href');
            const editHrefParts = currentEditHref.split('/');
            editHrefParts[editHrefParts.length - 1] = selectedValue; // Replace the last part with the selected value
            const newEditUrl = editHrefParts.join('/'); // Join the parts back into a new URL
            $editLink.attr('href', newEditUrl); // Update the href of the edit link
        }

        // Update the delete link within this form group
        const $deleteLink = $formGroup.find('[id^="deleteopsilink-"]');
        if ($deleteLink.length) {
            const currentDeleteHref = $deleteLink.attr('href');
            const deleteHrefParts = currentDeleteHref.split('/');
            deleteHrefParts[deleteHrefParts.length - 1] = selectedValue; // Replace the last part with the selected value
            const newDeleteUrl = deleteHrefParts.join('/'); // Join the parts back into a new URL
            $deleteLink.attr('href', newDeleteUrl); // Update the href of the delete link
        }
    });
    $('#pilihanjenisbahan').on('change', function() {
        updateOpsiDetail({{ $jenisbahan[0]->idvendor }}, {{ $jenisbahan[0]->idlayanan }}, this);
    });
    $(document).on('click', '.edit-button', function(event) {
        var button = $(this);
        var label = button.siblings('label');
        var currentValue = label.text();
        var detailId = button.data('id');
        
        $('#editInput').val(currentValue);
        $('#detailId').val(detailId);
        $('#editModal').modal('show');
    });

    $('#saveChanges').on('click', function() {
        var newValue = $('#editInput').val();
        var detailId = $('#detailId').val();
    
        $.ajax({
            url: `/detail/${detailId}`,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                value: newValue
            },
            success: function(response) {
               
                var targetElement = $('#detail-' + detailId);          
                
                targetElement.text(newValue);
                    
                $('#editModal').modal('hide');
            },
            error: function(xhr, status, error) {
                console.error('Error updating detail name:', error);
            }
        });
    });
    
   



    function updateOpsiDetail(idvendor, idlayanan, selectElement) {
    const idjenisbahan = $(selectElement).val(); 
    $.ajax({
        url: `/layanans/${idvendor}/details/${idlayanan}/${idjenisbahan}`, 
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#listOpsi').html(''); // Clear the existing options
            let html = '';

            if (response.result === 'success' && Array.isArray(response.data)) {
                let opsiDetail = {};
                
                response.data.forEach(function(item) {
                    const detailid = item.detail.id;

                    if (!opsiDetail[detailid]) {
                        opsiDetail[detailid] = {
                            id: detailid,
                            value: item.detail.value,
                            opsi: []
                        };
                    }

                    item.opsi.forEach(function(opsi) {
                        opsiDetail[detailid].opsi.push({
                            idopsi: opsi.id,
                            opsi: opsi.opsi,
                            biaya_tambahan: opsi.biaya_tambahan
                        });
                    });
                });

                // Construct the HTML for each detail and its options
                for (let key in opsiDetail) {
                    const detail = opsiDetail[key];
                    html += `
                        <div class="form-group px-4 py-2 col-sm-5">
                            <label id="detail-${key}">${detail.value}</label>
                            <button type="button" class="btn btn-secondary btn-sm mx-2 edit-button" data-id="${key}">Edit Detail</button>
                            <button type="button" class="btn btn-danger btn-sm mx-2 delete-button" 
                                    data-id="${detail.id}" 
                                    data-vendor="${idvendor}" 
                                    data-layanan="${idlayanan}" 
                                    data-toggle="modal" 
                                    data-target="#confirmDeleteModal">Delete
                                </button>
                            <span>
                                `;
                                    if(detail.opsi.length >0){
                                        html += `<div class="select-container">
                                            <select class="form-control custom-select" name="pilihanopsi" id="pilihanopsi-${key}">`
                                            detail.opsi.forEach(function(option) {
                                            html += `
                                                <option value="${option.idopsi}">${option.opsi}</option>
                                            `;
                                            });
                                        html += `
                                        </select>
                                        <span class="caret-down-icon"><i class="fas fa-caret-down"></i></span>
                                        </div>`
                                    }
                                html += `
                   
                            </span>
                            <div class="py-2">`;
                                if(detail.opsi.length >0){
                                    html += `<a id="editopsilink-${key}" href="/opsidetail/${idvendor}/${idlayanan}/${detail.opsi[0].idopsi}" class="btn btn-primary">Edit Opsi</a>`
                                }
                                
                                html += `<a id="addopsilink-${key}" href="/addopsidetail/${idvendor}/${idlayanan}/${key}" class="btn btn-primary">Tambah Opsi</a>`
                                if(detail.opsi.length >0){
                                    html += `<a id="deleteopsilink-${key}" href="/deleteopsidetail/${idvendor}/${idlayanan}/${detail.opsi[0].idopsi}" class="btn btn-danger">Hapus Opsi</a>`
                                }
                                
                                html += `
                            </div>
                        </div>
                    `;
                }
            } else {
                html = '<p>No Options required.</p>';
            }

            $('#listOpsi').html(html);
            $('#editHarga').html(
                `<a href="/layanancetak/editharga/${idjenisbahan}" class="btn btn-primary">Edit Harga</a>
            `);

            //Link button u/ jenis bahan
            html = `
                <a href="/vendors/${idvendor}/layanan/${idlayanan}/edit/${idjenisbahan}" 
                    class="btn btn-primary">
                    Edit Opsi
                </a>
            `
            $('#jenis-bahan-actions').html(html);

            
            $('#tambahdetail').html(`<a href="/createdetail/${idvendor}/${idlayanan}/${idjenisbahan}"  class="btn btn-primary">Tambah Detail</a>`)

            // Add change event listeners to the new select elements
            $('[id^="pilihanopsi-"]').on('change', function() {
                const $selector = $(this); // Use the changed select element
                const selectedValue = $selector.val();
                const $formGroup = $selector.closest('.form-group');

                const $editLink = $formGroup.find('[id^="editopsilink-"]');
                if ($editLink.length) {
                    const currentEditHref = $editLink.attr('href');
                    const editHrefParts = currentEditHref.split('/');
                    editHrefParts[editHrefParts.length - 1] = selectedValue; // Update the last part
                    const newEditUrl = editHrefParts.join('/');
                    $editLink.attr('href', newEditUrl); // Set the new href
                } 

                const $deleteLink = $formGroup.find('[id^="deleteopsilink-"]');
                if ($deleteLink.length) {
                    const currentDeleteHref = $deleteLink.attr('href');
                    const deleteHrefParts = currentDeleteHref.split('/');
                    deleteHrefParts[deleteHrefParts.length - 1] = selectedValue; // Update the last part
                    const newDeleteUrl = deleteHrefParts.join('/');
                    $deleteLink.attr('href', newDeleteUrl); // Set the new href
                }
            });
        }
    });
}
});
</script>
@endsection