<?php
namespace App\Supports\Api;


use Illuminate\Support\Facades\Log;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;


trait FacebookPixelConversationApi
{
    /**
     * Returns a Facebook Pixel Conversation API.
     *
     * @param array $event_info An array of information about the event (required).
     * @param array $user_info An array of information about the user (required).
     * @param mixed $event_content_data Data about the content of the event (required) if $event_content_data['event_type'] == 'Purchase'.
     * @return \FacebookAds\Api The Facebook Pixel Conversation API object.
     */
    function send_event(array $event_info, array $user_info, array $event_content_data = null)
    {
        return true;
        $access_token = env('FACEBOOK_CONVERSATION_ACT');
        $pixel_id = env('FACEBOOK_PIXEL_ID');

        if (empty($access_token) || empty($pixel_id)) {
            return;
        }
        $api = Api::init(null, null, $access_token);
        $api->setLogger(new CurlLogger());


        $user_data = (new UserData())
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setFbc('fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890')
            ->setFbp('fb.1.1558571054389.1098115397');
        if (count($user_info['email']) > 0 && !is_null($user_info['email'][0])) {
            $user_data->setEmails($user_info['email']);
        }
        if (count($user_info['phone']) > 0 && !is_null($user_info['phone'][0])) {
            $user_data->setPhones($user_info['phone']);
        }

        if ($event_info['event_type'] == 'Purchase') {
            $content = (new Content())
                ->setProductId($event_content_data['id'])
                ->setQuantity($event_content_data['qty']);

            $custom_data = (new CustomData())
                ->setContents(array($content))
                ->setCurrency($event_content_data['currency'])
                ->setValue($event_content_data['value']);
        }


        $event = (new Event())
            ->setEventName($event_info['event_type'])
            ->setEventTime(time())
            ->setEventSourceUrl($event_info['event_source_url'])
            ->setUserData($user_data)
            ->setActionSource(ActionSource::WEBSITE);

        if ($event_info['event_type'] == 'Purchase') {
            $event->setCustomData($custom_data);
        }

        $events = array();
        array_push($events, $event);

        $request = (new EventRequest($pixel_id))
            ->setEvents($events);
        $response = $request->execute();



        try {
            $response = $request->execute();
            // Log::info('Facebook Business SDK Response: ' . print_r($response, true));
            return $response;
        } catch (FacebookAds\Http\Exception\RequestException $e) {
            Log::error('Error executing Facebook Business SDK request: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred: ' . $e->getMessage());
        }
    }
}