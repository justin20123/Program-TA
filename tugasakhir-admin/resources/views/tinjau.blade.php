@extends('layout.sneat')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Vendors</a></li>
    <li class="breadcrumb-item active" aria-current="page">Vendor 3</li>
</ol>
@endsection

@section('menu')
<div class="container">
    <h2 class="mt-4">Vendor 3</h2>
    <h4 class="mt-4">Comments</h4>
    
    @foreach (range(1, 7) as $comment)
    <div class="card mb-3 border-primary">
        <div class="card-body d-flex">
            <!-- User Profile Picture -->
            <div class="me-3">
                <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User Profile">
            </div>
            <div>
                <!-- User Name and Rating -->
                <h5 class="card-title mb-1">John Doe</h5>
                <div class="text-warning">
                    @for ($i = 0; $i < 5; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                </div>
                <!-- Comment Text -->
                <p class="card-text mt-2">
                    I just tried this recipe and it was amazing! The instructions were clear and easy to follow, 
                    and the end result was delicious. I will definitely be making this again. Thanks for sharing!
                </p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('script')
<script>
    // Additional scripts (if needed)
</script>
@endsection