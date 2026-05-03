<?php

namespace App\Http\Controllers;

use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;

class SamityController extends Controller
{
    public function index(Request $request)
    {
        $query = Samity::withCount("members");
        if ($request->filled("search")) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where("name", "like", "%$s%")->orWhere("description", "like", "%$s%");
            });
        }
        if ($request->filled("cycle_type")) { $query->where("cycle_type", $request->cycle_type); }
        if ($request->filled("status"))     { $query->where("is_active", $request->status === "active"); }
        $samities = $query->latest()->paginate(10)->withQueryString();
        $stats = [
            "total"    => Samity::count(),
            "active"   => Samity::where("is_active", true)->count(),
            "inactive" => Samity::where("is_active", false)->count(),
            "members"  => \App\Models\User::count(),
        ];
        return view("samities.index", compact("samities", "stats"));
    }

    public function show(Samity $samity)
    {
        $samity->loadCount("members");
        $samity->load(["members" => function ($q) {
            $q->withPivot("joined_at", "is_active")->orderByPivot("joined_at", "desc");
        }]);
        $assignedIds    = $samity->members->pluck("id");
        $availableUsers = User::whereNotIn("id", $assignedIds)->where("is_active", true)->orderBy("name")->get();
        $summary = [
            "total_deposits" => $samity->deposits()->sum("amount"),
            "total_loans"    => $samity->loans()->sum("amount"),
            "active_loans"   => $samity->loans()->where("status", "active")->count(),
            "total_fines"    => $samity->fines()->sum("amount"),
        ];
        return view("samities.show", compact("samity", "availableUsers", "summary"));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name"           => ["required", "string", "max:255", "unique:samities"],
            "description"    => ["nullable", "string"],
            "cycle_type"     => ["required", "in:weekly,monthly,yearly"],
            "deposit_amount" => ["required", "numeric", "min:0"],
            "start_date"     => ["nullable", "date"],
            "meeting_day"    => ["nullable", "string", "max:50"],
            "is_active"      => ["boolean"],
        ]);
        $data["is_active"] = $request->boolean("is_active", true);
        Samity::create($data);
        return redirect()->route("samities.index")->with("success", "Samity created successfully.");
    }

    public function edit(Samity $samity) { return redirect()->route("samities.index"); }

    public function update(Request $request, Samity $samity)
    {
        $data = $request->validate([
            "name"           => ["required", "string", "max:255", "unique:samities,name," . $samity->id],
            "description"    => ["nullable", "string"],
            "cycle_type"     => ["required", "in:weekly,monthly,yearly"],
            "deposit_amount" => ["required", "numeric", "min:0"],
            "start_date"     => ["nullable", "date"],
            "meeting_day"    => ["nullable", "string", "max:50"],
            "is_active"      => ["boolean"],
        ]);
        $data["is_active"] = $request->boolean("is_active");
        $samity->update($data);
        return redirect()->route("samities.index")->with("success", "Samity updated successfully.");
    }

    public function destroy(Samity $samity)
    {
        if ($samity->members()->count() > 0) {
            return redirect()->route("samities.index")->with("error", "Cannot delete samity with active members.");
        }
        $samity->delete();
        return redirect()->route("samities.index")->with("success", "Samity deleted successfully.");
    }

    public function assignMember(Request $request, Samity $samity)
    {
        $request->validate([
            "user_id"   => ["required", "exists:users,id"],
            "joined_at" => ["required", "date"],
        ]);
        if ($samity->members()->where("user_id", $request->user_id)->exists()) {
            return back()->with("error", "Member is already assigned to this samity.");
        }
        $samity->members()->attach($request->user_id, ["joined_at" => $request->joined_at, "is_active" => true]);
        return back()->with("success", "Member assigned successfully.");
    }

    public function removeMember(Samity $samity, User $user)
    {
        $samity->members()->detach($user->id);
        return back()->with("success", "Member removed from samity.");
    }

    public function toggleMember(Samity $samity, User $user)
    {
        $pivot     = $samity->members()->where("user_id", $user->id)->first()?->pivot;
        $newStatus = $pivot ? !$pivot->is_active : true;
        $samity->members()->updateExistingPivot($user->id, ["is_active" => $newStatus]);
        return back()->with("success", "Member status updated.");
    }
}
