@extends('reports.pdf.layout')

@section('content')
    <table class="data-table">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $idx => $cell)
                        <td class="{{ $idx === 0 || in_array(strtolower($headers[$idx] ?? ''), ['tanggal', 'jam', 'status', 'no. rm']) ? 'text-center' : '' }}">
                            {{ $cell }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center" style="color: #64748b;">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
