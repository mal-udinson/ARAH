@extends('layouts.app')

@section('title', 'Edit Admin Kampus - Admin ARAH')

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
            Edit Admin Kampus
        </h1>

        <p style="
            margin:7px 0 0;
            font-size:13px;
            color:#64748b;
        ">
            Perbarui data administrator kampus.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('admin-arah.admin-kampus.update', $adminKampus->id) }}"
        style="
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:16px;
            padding:25px;
        "
    >

        @csrf
        @method('PUT')

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
                    value="{{ old('name', $adminKampus->name) }}"
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

            </div>


            <div>

                <label style="font-size:12px;font-weight:500;color:#334155;">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $adminKampus->email) }}"
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

            </div>


            <div>

                <label style="font-size:12px;font-weight:500;color:#334155;">
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Kosongkan jika tidak diubah"
                    style="
                        width:100%;
                        box-sizing:border-box;
                        padding:10px;
                        margin-top:6px;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                    "
                >

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

                    @foreach ($kampus as $item)

                        <option
                            value="{{ $item->id }}"
                            @selected(old('kampus_id', $adminKampus->kampus_id) == $item->id)
                        >
                            {{ $item->nama }} ({{ $item->kode }})
                        </option>

                    @endforeach

                </select>

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
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

@endsection