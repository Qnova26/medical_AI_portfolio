@extends('layouts.dashboard')

@section('content')

<div class="glass-card p-8">

    <h1 class="text-3xl font-bold mb-6">

        Prompt Comparison Result

    </h1>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr>

                    <th class="text-left py-3">
                        Prompt
                    </th>

                    <th class="text-left py-3">
                        Diagnosis
                    </th>

                    <th class="text-left py-3">
                        Confidence
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($results as $result)

                <tr class="border-b">

                    <td class="py-3">

                        {{ $result['prompt'] }}

                    </td>

                    <td>

                        {{ $result['diagnosis'] }}

                    </td>

                    <td>

                        {{ $result['confidence'] }}%

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection