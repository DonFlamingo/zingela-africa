<?php namespace ModalHelpers;

use Facades\Repositories\EventRepo;

class EventModalHelper extends ModalHelper {

    public function search($search) {
        $events = EventRepo::whereUserIdWithAttributes($this->user->id, $search);

        foreach ($events as &$event) {
            $event->time = tdate($event->time, $this->user->zone);
        }

        if ($this->api) {
            foreach ($events as &$event) {
                unset($event->geofence, $event->device);
            }

            $events = $events->toArray();
            $events['url'] = route('api.get_events');
        }

        return $events;
    }

    public function destroy() {
        EventRepo::updateWhere(['user_id' => $this->user->id, 'deleted' => 0], ['deleted' => 1]);
    }
}