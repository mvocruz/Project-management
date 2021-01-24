<?php

namespace App\Http\Controllers;
use App\Project;
use App\ProjectBulletinBoard;
use App\UsersProject;
use App\User;
use App\Ticket;
use App\StatusName;
use Auth;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return $projects->toJSON();
    }
    public function store(Request $request)
    {
        print_r($request->input('email'));
        // $tickets = new Ticket;
        // $tickets->description = $request->input('description');
        // $tickets->save();
    }
    public function complete(Request $request,$id) // post request
    {
        $status = 3;
        $finish = Project::where('id','=',$id)
                    ->first();
        $finish->status_id = $status;
        $finish->save();
        return response()->json([
            "message" => "Project Finalized"
          ], 201);
    }

    public function cancel(Request $request,$id) // post request
    {
        $status = 7;
        $finish = Project::where('id','=',$id)
                    ->first();
        $finish->status_id = $status;
        $finish->save();
        return response()->json([
            "message" => "Project Cancelled"
          ], 201);
    }

    public function change_start(Request $request,$id) // post request
    {
        $project = Project::where('id','=',$id)
                    ->first();
        $project->start_date = $request->input('start_date');
        $project->save();
        return response()->json([
            "message" => "Start Date Changed"
          ], 201);
    }

    public function change_deadline(Request $request,$id) // post request
    {
        $project = Project::where('id','=',$id)
                    ->first();
        $project->deadline = $request->input('deadline');
        $project->save();
        return response()->json([
            "message" => "Deadline Changed"
          ], 201);
    }


    public function delete_member($id) // get request
    {
        $res = UsersProject::where('id','=',$id)
                ->delete();
        return response()->json([
            "message" => "Member Removed from Project"
        ], 201);

    }
    public function show($id)
    {
        $projectInfo = Project::where('id','=',$id)
                        -> first();
         $status = StatusName::find($projectInfo->status_id);
         $projectInfo->status_name = $status->name;
         $projectInfo->status_description = $status->description;

         return $projectInfo;

    }

    public function tickets_per_project($id)
    {
        $tickets = Ticket::where('project_id','=',$id)
                    ->join('projects', 'projects.id', '=', 'tickets.project_id')
                    ->join('status_names', 'status_names.id', '=', 'tickets.status_id')
                    ->join('priority_names', 'priority_names.id', '=', 'tickets.priority_level')
                    ->select('tickets.*', 'projects.name as project_name','status_names.name as status_name', 'status_names.description as status_description', 'priority_names.name as priority_name', 'priority_names.description as priority_description')
                    -> get();
        return $tickets;
    }

    public function tickets_project_user($project_id, $user_id)
    {
        // $user = auth()->user();
                // print_r($user);
        $tickets = Ticket::where('project_id','=',$project_id)
                    ->where('receiver_id','=',$user_id)
                    ->join('projects', 'projects.id', '=', 'tickets.project_id')
                    ->join('status_names', 'status_names.id', '=', 'tickets.status_id')
                    ->join('priority_names', 'priority_names.id', '=', 'tickets.priority_level')
                    ->select('tickets.*', 'projects.name as project_name','status_names.name as status_name', 'status_names.description as status_description', 'priority_names.name as priority_name', 'priority_names.description as priority_description')
                    -> get();
         return $tickets;
    }

    public function add_member(Request $request,$id) // post request
    {
        $user_email = $request->input('email');
        $user_id = User::where('email','=',$user_email)
                    ->first();
        $member = new UsersProject;
        $member->project_id = $request->$id;
        $member->user_id = $user_id['id'];
        $member->isManager = $request->input('isManager');

        $member->save();

        return response()->json([
            "message" => "New Member added"
        ], 201);
    }

    public function fetch_member($id)
    {
        $comments = UsersProject::where('project_id','=',$id)
                        ->join('projects', 'projects.id', '=', 'users_projects.project_id')
                        ->join('users', 'users.id', '=', 'users_projects.user_id')
                        ->select('users.*','users_projects.project_id', 'projects.name as project_name')
                        -> get();
        return $comments;
    }

    public function fetch_comment($id)
    {
        $comments = ProjectBulletinBoard::where('project_id','=',$id)
                        ->join('projects', 'projects.id', '=', 'project_bulletin_boards.project_id')
                        ->join('users', 'users.id', '=', 'project_bulletin_boards.user_id')
                        ->select('project_bulletin_boards.*', 'projects.name as project_name','users.first_name as user_first_name','users.last_name as user_last_name','users.email as user_email')
                        -> get();
        return $comments;
    }

}
