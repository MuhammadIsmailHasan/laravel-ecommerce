@extends('layouts.admin')

@section('title')
    <title>Edit Product</title>
@endsection

@section('content')
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Product</li>
    </ol>

    <div class="container-fluid">
        <div class="animated fadeIn">
            <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Product</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" 
                                            class="form-control" placeholder="Name"
                                            value="{{ $product->name }}" required>
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="name">Description</label>
                                    <textarea name="description" id="description" class="form-control" required>
                                        {{ $product->description }}
                                    </textarea>
                                    <p class="text-danger">{{ $errors->first('description') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $product->status == '1' ? 'selected' : ''}}>Publish</option>
                                        <option value="0" {{ $product->status == '0' ? 'selected' : ''}}>Draft</option>
                                    </select>
                                    <p class="text-danger">{{ $errors->first('status') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="category_id">Kategory</label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Pilih</option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->id }}" {{ $product->category_id == $item->id ? 'selected' : ''}}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger">{{ $errors->first('category_id') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="number" name="price" id="price" 
                                            class="form-control" placeholder="Price"
                                            value="{{ $product->price }}" required>
                                    <p class="text-danger">{{ $errors->first('price') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Berat</label>
                                    <input type="number" name="weight" id="weight" 
                                            class="form-control" placeholder="Berat"
                                            value="{{ $product->weight }}" required>
                                    <p class="text-danger">{{ $errors->first('weight') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <br>

                                    <img src="{{ asset('storage/products/' . $product->image) }}" 
                                        alt="{{ $product->name }}"
                                        width="100" height="100">
                                    <hr>
                                    
                                    <input type="file" name="image" id="image" class="form-control">
                                    <p><strong>Biarkan kosong jika tidak ingin mengganti</strong></p>
                                    <p class="text-danger">{{ $errors->first('image') }}</p>
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</main>
@endsection

@section('js')
<script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('description');
</script>
@endsection