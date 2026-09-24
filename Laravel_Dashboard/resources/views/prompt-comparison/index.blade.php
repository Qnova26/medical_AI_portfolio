@extends('layouts.dashboard')

@section('content')

<div class="glass-card p-8">

    <h1 class="text-3xl font-bold mb-6">
        Prompt Comparison
    </h1>

    <form
        action="{{ route('prompt-comparison.run') }}"
        method="POST">

        @csrf

        <div>

            <label class="font-semibold">
                Clinical Input
            </label>

            <textarea
                name="input"
                rows="6"
                class="w-full mt-2 rounded-xl"></textarea>

        </div>

        <button
            class="mt-6 px-6 py-3 rounded-xl bg-cyan-600 text-white">

            Compare Prompts

        </button>

    </form>

</div>

@endsection