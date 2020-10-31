<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Model\Student;
use DB;
class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $student=Student::all();
       return response()->json($student);
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Hash::make($data['password'])
        // $validatedData = $request->validate([
        //     'class_id' => 'required',
        //     'subject_name' => 'required|unique:subjects|max:25',
        //     'subject_code' => 'required|unique:subjects|max:25'
        // ]);
        $data=array();
        $data['class_id']=$request->class_id;
        $data['name']=$request->name;
        $data['phone']=$request->phone;
        $data['email']=$request->email;
        $data['password']=Hash::make($request->password);
        $data['gender']=$request->gender;
        $data['photo']=$request->photo;
        DB::table('students')->insert($data);
        return response('inserted');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $show = Student::findorfail($id);
       return response()->json($show);
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
        $data=array();
        $data['class_id']=$request->class_id;
        $data['name']=$request->name;
        $data['phone']=$request->phone;
        $data['email']=$request->email;
        $data['password']=Hash::make($request->password);
        $data['gender']=$request->gender;
        $data['photo']=$request->photo;
        DB::table('students')->where('id',$id)->update($data);
        return response('updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $show = Student::findorfail($id);
        $img_path = $show->photo;
        unlink($img_path);
        Student::where('id',$id)->delete();
        return response('deleted');
    }
}
