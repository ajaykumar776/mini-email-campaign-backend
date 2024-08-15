<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $userId;
    protected $chunkSize;

    public function __construct(Campaign $campaign, $userId, $chunkSize = 500)
    {
        $this->campaign = $campaign;
        $this->userId = $userId;
        $this->chunkSize = $chunkSize;
    }

    public function handle()
    {
        try {
            $user = $this->getUser($this->userId);
            if (!$user) {
                Log::error('User not found', ['user_id' => $this->userId]);
                return;
            }

            $lines = $this->getCsvLines($this->campaign->csv_file);
            if (empty($lines)) {
                Log::error('CSV file is empty or invalid', ['campaign_id' => $this->campaign->id]);
                return;
            }

            $processedLines = $this->processCsvLines($lines);

            if ($this->isCampaignProcessed($processedLines, count($lines))) {
                $this->markCampaignAsProcessed();
                $this->notifyUserOfCompletion($user);
            }
        } catch (\Exception $e) {
            $this->handleProcessingError($e);
        }
    }

    protected function getUser($userId)
    {
        return User::find($userId);
    }

    protected function getCsvLines($csvFilePath)
    {
        $file = Storage::get($csvFilePath);
        return explode("\n", $file);
    }

    protected function processCsvLines(array $lines)
    {
        $headerSkipped = false;
        $processedLines = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if ($this->processSingleLine($line)) {
                $processedLines++;
            }

            if ($processedLines >= $this->chunkSize) {
                break;
            }
        }

        return $processedLines;
    }

    protected function processSingleLine($line)
    {
        $data = str_getcsv($line);

        if (count($data) != 2) {
            Log::warning('Invalid CSV line', ['line' => $line]);
            return false;
        }

        [$name, $email] = array_map('trim', $data);

        if ($this->validateEmail($email)) {
            $this->sendEmail($name, $email);
            return true;
        }

        return false;
    }

    protected function validateEmail($email)
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            Log::warning('Invalid email address', ['email' => $email, 'errors' => $validator->errors()]);
            return false;
        }

        return true;
    }

    protected function sendEmail($name, $email)
    {
        Mail::send('emails.campaign_template', ['username' => $name], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Campaign Email')
                    ->attach(public_path('15august.jpeg'), [
                        'as' => '15-august.jpeg',
                        'mime' => 'image/jpeg',
                    ]);
        });
    }
    

    protected function isCampaignProcessed($processedLines, $totalLines)
    {
        return ($processedLines + 1) >= $totalLines;
    }

    protected function markCampaignAsProcessed()
    {
        $this->campaign->update(['status' => 'processed']);
    }

    protected function notifyUserOfCompletion($user)
    {
        Mail::raw("The campaign '{$this->campaign->name}' has been processed successfully.", function ($message) use ($user) {
            $message->to($user->email)->subject('Campaign Processed');
        });
    }

    protected function handleProcessingError(\Exception $e)
    {
        Log::error('Error processing campaign', ['error' => $e->getMessage()]);
        $this->campaign->update(['status' => 'failed']);
    }
}
