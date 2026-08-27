<html>
    <head>
        <title>Home</title>
    </head>
    <body>
        <h1>Welcome to the Home Page</h1>
        @foreach ($produk as $data)
        <h2>{{ $data->nama_barang }}</h2>
        <p>Price: {{ $data->harga_barang }}</p>
        <p>category: {{ $data->kategori->nama_kategori }}</p>

        @endforeach

    </body>
    </html>
