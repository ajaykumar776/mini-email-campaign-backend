<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaignEmail;
use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CampaignController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'csv_file' => 'required|file|mimes:csv,txt',
            ]);

            $csvFilePath = $request->file('csv_file')->store('campaigns');

            $campaign = Campaign::create([
                'name' => $request->name,
                'csv_file' => $csvFilePath,
                'user_id' => $request->user()->id,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Campaign is being processed. You will be notified via email once the campaign is done.',
                'campaign_id' => $campaign->id
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create campaign'], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $campaigns = $request->user()->campaigns()->get();

            return response()->json(['campaigns' => $campaigns]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch campaigns'], 500);
        }
    }
    public function deleteCampaign(Request $request, $campaignId)
    {
        try {
            $user = $request->user();
            $campaign = Campaign::where('id', $campaignId)->where('user_id', $user->id)->firstOrFail();

            if ($campaign->csv_file) {
                Storage::delete($campaign->csv_file);
            }

            // Delete the campaign
            $campaign->delete();

            return response()->json(['message' => 'Campaign deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campaign not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting campaign', 'error' => $e->getMessage()], 500);
        }
    }

    public function getStatus(Request $request, $campaignId)
    {
        try {
            $user = $request->user();
            $campaign = Campaign::where('id', $campaignId)->where('user_id', $user->id)->firstOrFail();
            
            return response()->json(['status' => $campaign->status]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Campaign not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching campaign status', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function downloadFile($filePath)
    {
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }
        $file = Storage::disk('public')->get($filePath);
        $fileName = basename($filePath);

        return response($file, 200)
            ->header('Content-Type', Storage::disk('public')->mimeType($filePath))
            ->header('Content-Disposition', "attachment; filename={$fileName}");
    }
}
