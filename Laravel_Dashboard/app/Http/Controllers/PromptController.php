<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prompt;

class PromptController extends Controller
{
    public function index()
    {
        $prompts = Prompt::latest()->get();

        return view('prompts.index', compact('prompts'));
    }

    public function create()
    {
        return view('prompts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'level' => 'required',
            'system_prompt' => 'required',
            'user_prompt' => 'required',
        ]);

        Prompt::create([
            'name' => $request->name,
            'category' => $request->category,
            'level' => $request->level,
            'status' => 'Active',
            'system_prompt' => $request->system_prompt,
            'user_prompt' => $request->user_prompt,
        ]);

        return redirect()
            ->route('prompts.index')
            ->with('success', 'Prompt Template created successfully');
    }

    public function show($id)
    {
        $prompt = Prompt::findOrFail($id);

        return view('prompts.show', compact('prompt'));
    }

    public function edit($id)
    {
        $prompt = Prompt::findOrFail($id);

        return view('prompts.edit', compact('prompt'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'level' => 'required',
            'status' => 'required|in:Active,Inactive',
            'system_prompt' => 'required',
            'user_prompt' => 'required',
        ]);

        $prompt = Prompt::findOrFail($id);

        $prompt->update([
            'name' => $request->name,
            'category' => $request->category,
            'level' => $request->level,
            'status' => $request->status,
            'system_prompt' => $request->system_prompt,
            'user_prompt' => $request->user_prompt,
        ]);

        return redirect()
            ->route('prompts.index')
            ->with('success', 'Prompt Template updated successfully');
    }

    public function destroy($id)
    {
        $prompt = Prompt::findOrFail($id);
        $prompt->delete();

        return redirect()
            ->route('prompts.index')
            ->with('success', 'Prompt Template deleted successfully');
    }
}