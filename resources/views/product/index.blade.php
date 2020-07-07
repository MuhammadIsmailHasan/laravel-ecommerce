@extends('layouts.admin')

@section('title')
    <title>List Product</title>
@endsection

@section('content')
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Product</li>
    </ol>

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">List Product</h4>
                            <a href="{{ route('product.bulk') }}" class="btn btn-success btn-sm">Mass Upload</a>
                            <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm float-right">Tambah</a>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-success">{{ session('error') }}</div>
                            @endif

                            {{-- Pencarian --}}
                            <form action="{{ route('product.index') }}" method="GET">
                                <div class="input-group mb-3 col-md-3 float-right">
                                    <input type="text" name="q" id="q" class="form-control"
                                        placeholder="Cari ..." 
                                        value="{{ request()->q }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">Cari</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($product as $row)
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('storage/products/' . $row->image) }}" 
                                                        alt="{{ $row->name}}"
                                                        width="100px"
                                                        height="100px">
                                                </td>
                                                <td>
                                                    <strong>{{ $row->name }}</strong><br>
                                                    <label>Kategori :
                                                        <span class="badge badge-info">{{ $row->category->name }}</span>
                                                    </label><br>
                                                    <label>Berat :
                                                        <span class="badge badge-info">{{ $row->weight }} gr</span>
                                                    </label>
                                                </td>
                                                <td>Rp. {{ number_format($row->price) }}</td>
                                                <td>{{ $row->created_at->format('d-m-Y') }}</td>
                                                <td>{!! $row->status_label !!}</td>
                                                <td>
                                                    <form action="{{ route('product.destroy', $row->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="{{ route('product.edit', $row->id) }}" 
                                                            class="btn btn-warning btn-sm">Edit</a>
                                                        
                                                        <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {!! $product->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection