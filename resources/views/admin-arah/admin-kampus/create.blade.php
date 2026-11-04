@extends('layouts.app')

@section('title', 'Tambah Admin Kampus - Admin ARAH')

@section('content')

<div style="max-width:900px;margin:0 auto;padding:35px 22px;">

    <div style="margin-bottom:25px;">

        <a
            href="{{ route('admin-arah.admin-kampus.index') }}"
            style="
                font-size:12px;
                color:#64748b;
                text-decoration:none;
            "
        >
            ← Kembali
        </a>

        <h1 style="
            margin:15px 0 0;
            font-size:27px;
            font-weight:600;
            color:#1e293b;
        ">
            Tambah Admin Kampus
        </h1>

        <p style="
            margin:7px 0 0;
            font-size:13px;
            color:#64748b;
        ">
            Buat akun administrator untuk sebuah kampus.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('admin-arah.admin-kampus.store') }}"
        style="
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:16px;
            padding:25px;
        "
    >

        @csrf

        <div style="
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:18px;
        ">

            <div style="grid-column:1 / -1;">

                <label style="font-size:12px;font-weight:500;color:#334155;">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    style="
                        width:100%;
                        box-sizing:border-box;
                        padding:10px;
                        margin-top:6px;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                    "
                >

                @error('name')
                    <div style="margin-top:5px;font-size:11px;color:#dc2626;">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div>

                <label style="font-size:12px;font-weight:500;color:#334155;">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    style="
                        width:100%;
                        box-sizing:border-box;
                        padding:10px;
                        margin-top:6px;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                    "
                >

                @error('email')
                    <div style="margin-top:5px;font-size:11px;color:#dc2626;">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div>

                <label style="font-size:12px;font-weight:500;color:#334155;">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    style="
                        width:100%;
                        box-sizing:border-box;
                        padding:10px;
                        margin-top:6px;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                    "
                >

                @error('password')
                    <div style="margin-top:5px;font-size:11px;color:#dc2626;">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div style="grid-column:1 / -1;">

                <label style="font-size:12px;font-weight:500;color:#334155;">
                    Kampus
                </label>

                <select
                    name="kampus_id"
                    required
                    style="
                        width:100%;
                        padding:10px;
                        margin-top:6px;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                        background:#fff;
                    "
                >

                    <option value="">
                        Pilih Kampus
                    </option>

                    @foreach ($kampus as $item)

                        <option
                            value="{{ $item->id }}"
                            @selected(old('kampus_id') == $item->id)
                        >
                            {{ $item->nama }} ({{ $item->kode }})
                        </option>

                    @endforeach

                </select>

                @error('kampus_id')
                    <div style="margin-top:5px;font-size:11px;color:#dc2626;">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>


        <div style="margin-top:25px;text-align:right;">

            <button
                type="submit"
                style="
                    border:0;
                    padding:10px 16px;
                    border-radius:9px;
                    background:#1e2a6b;
                    color:#fff;
                    font-size:12px;
                    font-weight:500;
                    cursor:pointer;
                "
            >
                Simpan Admin
            </button>

        </div>

    </form>

</div>

@endsection