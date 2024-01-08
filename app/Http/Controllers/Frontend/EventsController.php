<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Facades\ModalHelpers\EventModalHelper;

class EventsController extends Controller {

    public function index() {
        $search = array_key_exists('search', $this->data) ? $this->data['search'] : '';
        $events = EventModalHelper::search($search);
        if (array_key_exists('data', $events)) {
            foreach ($events['data'] as &$event) {
                if (empty($event))
                    continue;

                $event['message'] = parseEventMessage($event['message'], $event['type']);
            }
        }
        else {
            foreach ($events as &$event) {
                if (empty($event))
                    continue;

                $event->message = parseEventMessage($event->message, $event->type);
            }
        }

        return !$this->api ? view('front::Events.index')->with(['events' => $events]) : ['status' => 1, 'items' => $events];
    }

    public function doDestroy() {
        return view('front::Events.destroy');
    }

    public function destroy() {
        EventModalHelper::destroy();

        return ['status' => 1];
    }
}