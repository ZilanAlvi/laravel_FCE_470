<?php

namespace App\Http\Controllers;

use App\Models\Player;

use App\Models\ClubBid;

use App\Models\Club;

use App\Models\Ranking;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

use App\Models\Matches;

use App\Models\MatchRequest;

use App\Models\User;

use App\Models\Ticket;

use App\Models\SponsorshipRequest;

use Illuminate\Support\Facades\Notification;

use App\Notifications\SendEmailNotification;

class AdminController extends Controller
{
    public function add_player_page()
    {
        $clubs = Club::all(); 
        return view('admin.Add_Player',compact('clubs'));
    }

    public function add_player_info(Request $request)
    {
    

    $experience = $request->experience;
    $goals = $request->goals;
    $assists = $request->assist;
    $minutesPlayed = $request->minutes_played;

    $rankingValue = $experience + ($goals * 2) + ($assists * 1.5) + ($minutesPlayed / 90);

    $newRankingValue = $rankingValue;
    


    $player = new Player;
    $player->name = $request->name;
    $player->age = $request->age;
    $player->height = $request->height;
    $player->weight = $request->weight;
    $player->contact = $request->contact;
    $player->address = $request->address;

    
    $club = Club::where('club_name', $request->club)->first();
    $player->club_id = $club ? $club->id : null; // Assuming the club_id is of nullable type
    $player->club = $request->club; // Optionally assign the club name // Assign club_id based on club_name
    //$player->club = $request->club; // Assign club based on club_name
    $player->club = $request->club;
   // $player->club_id = $request->club;
    $player->position = $request->position;
    $player->expeirence = $request->experience;
    $player->goals = $request->goals ?: 0;
    $player->assists = $request->assist ?: 0;
    $player->minsplayed = $request->minutes_played ?: 0;
    $player->ranking_value = $newRankingValue;
    $player->rank = 0;
   
 
    // Handle image upload
    if ($request->hasFile('pimage')) {
        $image = $request->file('pimage');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move('player_images', $imageName);
        $player->pimage = $imageName;
    }
    //$player->club_id = Club::where('club_name', $request->club)->value('user_id'); // Assign club_id based on club_name
   // $player->club = $request->club; 
  
    $player->save();


    $players = Player::all();
            $existingPlayers = $players->sortByDesc('ranking_value');
            $rank = 1;
            foreach ($existingPlayers as $player) {
                // Update the desired column value for each player
                $player->rank = $rank; 
                $player->save(); // Save the changes to the database
                $rank++;
            }

    
    return redirect()->back()->with('message', 'Player Added Successfully');
    }


    public function showPendingBids()
    {
        $pendingBids = ClubBid::with(['club.clubBids', 'player.clubBids'])
        ->where('is_accepted', false)
        ->where('is_declined', false)
        ->get();

    return view('admin.pending_bids', ['bids' => $pendingBids]);
    }


    public function acceptBid($bidId)
    {
        $bid = ClubBid::findOrFail($bidId);
        $bid->is_accepted = true;
        $bid->save();
        $player = $bid->player->load('club');
      //  dd($player->toArray());
        $newClub = $bid->club;

        // Retrieve the club based on the club name in the player table
       // $club = Club::where('club_name', $player->club)->first();
       // dd($newClub->toArray());
        // Update player's club information
        if ($newClub) {
            $player->update([
                'club_id' => $newClub->user_id,
                'club' => $newClub->club_name,
            ]);
        }
        //dd($player->toArray());
        Session::flash('message', 'Bid accepted');
        return redirect()->back();

        //Session::flash('message', 'Bid accepted');
        //return redirect()->back();
    }

    public function generate_rating_page()
        {
            $players = Player::orderBy('rank', 'asc')->get(); // sort by rank
            return view('admin.Generate_rating', compact('players'));
        }

    public function find_player_ranking(Request $request)
    {
        // Retrieve the player's name from the request
        $playerName = $request->name;

        // Check if the player exists in the players table
        $player = Player::where('name', $playerName)->first();

        if ($player) {
            // Player exists in the players table
            // Continue saving the ranking information


            // calculate the ranking_value
            $experience = $request->experience;
            $goals = $request->goals;
            $assists = $request->assist;
            $minutesPlayed = $request->minutes_played;

            $rankingValue = $experience + ($goals * 2) + ($assists * 1.5) + ($minutesPlayed / 90);

            $newRankingValue = $rankingValue;



            $ranking = new Ranking;
            $ranking->name = $request->name;
            $ranking->age = $request->age;
            $ranking->height = $request->height;
            $ranking->weight = $request->weight;
            $ranking->playing_position = $request->playing_position;
            $ranking->experience = $request->experience;
            $ranking->goals = $request->goals;
            $ranking->assist = $request->assist;
            $ranking->minutes_played = $request->minutes_played;
            $ranking->ranking_value = $newRankingValue;
            //$ranking->rank = $rank;

            // Handle image upload
            if ($request->hasFile('pimage')) {
                $image = $request->file('pimage');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move('player_images', $imageName);
                $ranking->pimage = $imageName;
            }

            $ranking->save();


            $players = Ranking::all();
            $existingPlayers = $players->sortByDesc('ranking_value');
            $rank = 1;
            foreach ($existingPlayers as $player) {
                // Update the desired column value for each player
                $player->rank = $rank; 
                $player->save(); // Save the changes to the database
                $rank++;
            }


            return redirect()->back()->with('message', 'Player Data Added and Rated Successfully');
        } else {
            // Player does not exist in the players table
            return redirect()->back()->with('error', 'The player does not exist in our database.');
        }
    }
    


    public function showPendingBids()
    {
        $pendingBids = ClubBid::with(['club.clubBids', 'player.clubBids'])
        ->where('is_accepted', false)
        ->where('is_declined', false)
        ->get();

    return view('admin.pending_bids', ['bids' => $pendingBids]);
    }


    public function acceptBid($bidId)
    {
        $bid = ClubBid::findOrFail($bidId);
        $bid->is_accepted = true;
        $bid->save();
        $player = $bid->player->load('club');
      //  dd($player->toArray());
        $newClub = $bid->club;

        // Retrieve the club based on the club name in the player table
       // $club = Club::where('club_name', $player->club)->first();
       // dd($newClub->toArray());
        // Update player's club information
        if ($newClub) {
            $player->update([
                'club_id' => $newClub->user_id,
                'club' => $newClub->club_name,
            ]);
        }
        //dd($player->toArray());
        Session::flash('message', 'Bid accepted');
        return redirect()->back();

        //Session::flash('message', 'Bid accepted');
        //return redirect()->back();
    }

    public function declineBid($bidId)
{
    $bid = ClubBid::findOrFail($bidId);
    $bid->is_declined = true;
    $bid->save();

    Session::flash('message', 'Bid declined');
    return redirect()->back();
}

    public function create_match_page()
    {
        $clubs = Club::all();
        return view('admin.create_match', compact('clubs'));
    }

    public function store_match(Request $request)
    {
        $request->validate([
            'team1' => 'required|exists:clubs,id',
            'team2' => 'required|different:team1|exists:clubs,id',
            'match_datetime' => 'required|date',
            'stadium' => 'required|string',
        ]);

        $matches = new Matches;
        $matches->team1_id = $request->team1;
        $matches->team2_id = $request->team2;
        $matches->match_datetime = $request->match_datetime;
        $matches->stadium = $request->stadium;
        $matches->save();

        return redirect()->route('admin.create_match_page')->with('message', 'Match created successfully');
    }
    public function view_matches()
    {
    $matches = Matches::all();

    foreach ($matches as $match) {
        $match->team1_name = Club::findOrFail($match->team1_id)->club_name;
        $match->team2_name = Club::findOrFail($match->team2_id)->club_name;
    }
    return view('admin.matches', compact('matches'));
    }

    public function view_match_requests()
{
    $matchRequests = MatchRequest::with(['club', 'team2'])->get();
    return view('admin.match_requests', compact('matchRequests'));
}
    public function approve_match_request($id)
    {
        $matchRequest = MatchRequest::findOrFail($id);
        $matchRequest->status = 'approved';
        $matchRequest->save();

        // Move the approved match request to the matches table
        $approvedMatch = new Matches();
        $approvedMatch->team1_id = $matchRequest->club_id;
        $approvedMatch->team2_id = $matchRequest->team2_id;
        $approvedMatch->match_datetime = $matchRequest->match_datetime;
        $approvedMatch->stadium = $matchRequest->stadium;
        $approvedMatch->save();

        //$matchRequest->delete(); if we want to delete from matchrequest table

        return redirect()->route('admin.view_match_requests')->with('message', 'Match request approved successfully');
    }

    public function decline_match_request($id)
    {
        $matchRequest = MatchRequest::findOrFail($id);
        $matchRequest->status = 'declined';
        $matchRequest->save();

        return redirect()->route('admin.view_match_requests')->with('message', 'Match request declined successfully');
    }

    public function email_sys()
    {
        $fans = User::where('usertype', 4)->get();
        return view('admin.email_fans', compact('fans'));
    }

    public function send_email($id)
    {
        $fans=User::find($id);
        return view('admin.email_info', compact('fans'));
    }

    public function send_fan_email(Request $request, $id)
    {
        $fans=User::find($id);
        $details = [
            'greeting' => $request->greeting,
            'body' => $request->body,
            'button' => $request->button,
            'url' => $request->url,
        ];

        Notification::send($fans, new SendEmailNotification($details));
        return redirect()->back();
    }

    public function ticket()
{
    $approvedMatches = Matches::with(['team1', 'team2', 'tickets'])->where('status', 'Approved')->get();
    return view('admin.ticket', compact('approvedMatches'));
}

    public function createTickets(Request $request, $matchId)
    {
        $request->validate([
            'seats' => 'required|integer|min:1',
        ]);

        $match = Matches::findOrFail($matchId);

        for ($i = 1; $i <= $request->seats; $i++) {
            
            Ticket::create([
                'match_id' => $match->id,
                'seat_number' => $i,
            ]);
        }

        return redirect()->back();
    }

    public function endMatch($id)
{
    $match = Matches::findOrFail($id);
    $match->match_status = 'ended'; // Set match status to 'ended'
    $match->save();
    return redirect()->route('admin.matchscore', ['id' => $match->id]);
}

    public function updateScores(Request $request, $id)
    {
        $match = Matches::findOrFail($id);
        $match->team1_score = $request->team1_score;
        $match->team2_score = $request->team2_score;
        $match->save();

        return redirect()->route('admin.view_matches')->with('message', 'Match scores updated');
    }
    public function matchscore($id)
    {
        $match = Matches::findOrFail($id);
        $match->team1_name = Club::findOrFail($match->team1_id)->club_name;
        $match->team2_name = Club::findOrFail($match->team2_id)->club_name;
        

        return view('admin.matchscore', compact('match'));
    }

    public function approveSponsorship($id)
    {
        $request = SponsorshipRequest::findOrFail($id);
        $request->status = 'approved';
        $request->save();

        $match = Matches::find($request->match_id);
        $match->sponsor_picture = $request->image_path;
        $match->save();

        return redirect()->back()->with('message', 'Sponsorship request approved');
    }

    public function declineSponsorship($id)
    {
        $request = SponsorshipRequest::findOrFail($id);
        $request->status = 'rejected';
        $request->save();

        return redirect()->back()->with('message', 'Sponsorship request declined');
    }

    public function sponsor_approval()
{
    $requests = SponsorshipRequest::with(['match.team1', 'match.team2'])->get(); 
    return view('admin.sponsor_approval', compact('requests'));
}

//==================================================




}
