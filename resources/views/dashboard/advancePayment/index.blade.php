
@extends('dashboard.layout.root')
@section('content')
    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Advance Payments</h1>
            @can('make advance payment')
            <a href="{{route('advance.create')}}" class="btn btn-primary">Make Advance Payment</a>
            @endcan
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Title</th>
                    <th class="py-3 px-6 text-left">Employee</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-left">Paid Amount</th>
                    <th class="py-3 px-6 text-left">Date</th>
                    <th class="py-3 px-6 text-left">Paid By</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($payments as $payment)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$payment->title}}</td>
                        <td class="py-3 px-6 text-left">{{$payment->user->name}}</td>
                        <td class="py-3 px-6 text-left">{{$payment->description}}</td>
                        <td class="py-3 px-6 text-left">{{$payment->amount}}</td>
                        <td class="py-3 px-6 text-left">{{$payment->date}}</td>
                        <td class="py-3 px-6 text-left">{{$payment->paidBy?->name}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
