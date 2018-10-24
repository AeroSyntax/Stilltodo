<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Todo;

class TodosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todos = Todo::orderBy('fulfilled', 'asc')->orderBy('created_at', 'desc')->orderBy('fulfilled_at', 'desc')->paginate(10);
        return view('todos.index')->with('todos', $todos);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('todos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'file_name' => 'nullable|max:5120'
        ]);
        
        // Handle File Upload
        if($request->hasFile('file_name')){
            // Get filename with the extension
            $filenameWithExt = $request->file('file_name')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('file_name')->getClientOriginalExtension();
            if(!in_array(strtolower($extension), ['jpeg','jpg','xml','pdf'])){
                return redirect('/dashboard')->with('error', 'Invalid file type!');
            }

            // Filename to store
            $fileNameToStore= $filename.'_'.time().'.'.$extension;
            // Upload file
            $path = $request->file('file_name')->storeAs('public/user_uploads', $fileNameToStore);
        } else {
            $fileNameToStore = '';
        }

        

        $desc = '';
        if(!is_null($request->input('description'))){
            $desc = $request->input('description');
        }
        //Create todo
        $todo = new Todo;
        $todo->user_id = auth()->user()->id;
        $todo->title = $request->input('title');
        $todo->description = $desc;
        $todo->fulfilled = 0;
        $todo->fulfilled_at = NULL;
        $todo->file_name = $fileNameToStore;

        $todo->save();
        return redirect('/dashboard')->with('success', 'Todo Created');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todo = Todo::find($id);
        return view('todos.show')->with('todo', $todo);
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $todo = Todo::find($id);

        //Check for correct user
        if(auth()->user()->id !==$todo->user_id){
            return redirect('/dashboard')->with('error', 'Unauthorized Page');
        }
        return view('todos.edit')->with('todo', $todo);
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'file_name' => 'nullable|max:5120'
        ]);

        // Handle File Upload
        if($request->hasFile('file_name')){
            // Get filename with the extension
            $filenameWithExt = $request->file('file_name')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('file_name')->getClientOriginalExtension();
            if(!in_array(strtolower($extension), ['jpeg','jpg','xml','pdf'])){
                return redirect('/dashboard')->with('error', 'Invalid file type!');
            }
            // Filename to store
            $fileNameToStore= $filename.'_'.time().'.'.$extension;
            // Upload file
            $path = $request->file('file_name')->storeAs('public/user_uploads', $fileNameToStore);
        } 

        $todo = Todo::find($id);
        $todo->title = $request->input('title');
        if(!is_null($request->input('description'))){
            $todo->description = $request->input('description');
        }
        if(!is_null($request->input('fulfilled'))){
            $todo->fulfilled = 1;
        }
        if($request->hasFile('file_name')){
            $todo->file_name = $fileNameToStore;
        }
        
        $todo->save();
        
        return redirect('/dashboard')->with('success', 'Todo Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $todo = Todo::find($id);
        // Check for correct user
        if(auth()->user()->id !==$todo->user_id){
            return redirect('/dashboard')->with('error', 'Unauthorized Page');
        }
        if($todo->file_name != ''){
            // Delete file
            Storage::delete('public/user_uploads/'.$todo->file_name);
        }

        $todo->delete();
        return redirect('/dashboard')->with('success', 'Todo Removed!');
    }


   
    // Search in todo title or description
    public function search(Request $request)
    {

        $todos = Todo::where('title', 'LIKE', '%'.$request->input('key').'%')->orWhere('description', 'LIKE', '%'.$request->input('key').'%')->orderBy('fulfilled', 'asc')->orderBy('created_at', 'desc')->orderBy('fulfilled_at', 'desc')->paginate(10);
        return view('todos.index')->with('todos', $todos);
    }
}