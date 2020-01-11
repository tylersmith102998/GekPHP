<?php

/**
 * Flash.php
 *
 * This class is responsible for handling backend errors on the front-end. Back-
 * end developers should use this class to set their errors that the user needs
 * to see, and front-end developers should use the front-end methods provided
 * in this class to generate your html structure.
 *
 * There are 3 types of messages the backend can send, those are error messages,
 * general notifications, and success messages.
 */

namespace HTML;

use \Networking\Session;

class Flash
{

    /**
     * Holds all of the errors.
     * @var array
     */
    private $errors = [];

    /**
     * Holds all of the notifications.
     * @var array
     */
    private $notifications = [];

    /**
     * Holds all of the success messages.
     * @var [type]
     */
    private $successes = [];

    private $error_pointer;

    private $notification_pointer;

    private $success_pointer;

    private $Session;

    public function __construct()
    {
        $this->Session = new Session();

        $this->load();

        $this->error_pointer = 0;
        $this->notification_pointer = 0;
        $this->success_pointer = 0;
    }

    public function load()
    {
        if ($this->Session->get('Flash'))
        {
            //exit('te');
            $data = $this->Session->get('Flash');
            $data = unserialize($data);

            $this->errors           = $data['errors'];
            $this->notifications    = $data['notifications'];
            $this->successes        = $data['successes'];
            $this->Session->destroy('Flash');
        }
    }

    public function save()
    {
        $data = [
            'errors'        => $this->errors,
            'notifications' => $this->notifications,
            'successes'     => $this->successes
        ];
        $data = serialize($data);

        $this->Session->set('Flash', $data);
    }

    /**
     * Sets an error message. Mainly for use in back-end programming.
     * @param  string $msg the message to pass to the front-end.
     * @return void
     */
    public function error($msg)
    {
        $this->errors[] = $msg;
    }

    /**
     * Sets a notification message. Mainly for use in back-end programming.
     * @param  string $msg the message to pass to the front-end.
     * @return void
     */
    public function notify($msg)
    {
        $this->notifications[] = $msg;
    }

    /**
     * Sets a success message. Mainly for use in back-end programming.
     * @param  string $msg the message to pass to the front-end.
     * @return void
     */
    public function success($msg)
    {
        $this->successes[] = $msg;
    }

    public function get()
    {
        $Ep = &$this->error_pointer;
        $Np = &$this->notification_pointer;
        $Sp = &$this->success_pointer;

        if (isset($this->errors[$Ep]))
        {
            $Ep++;
            return [
                'type' => 'error',
                'text' => $this->errors[$Ep-1]
            ];
        }
        else if (isset($this->notifications[$Np]))
        {
            $Np++;
            return [
                'type' => 'notification',
                'text' => $this->notifications[$Np-1]
            ];
        }
        else if (isset($this->successes[$Sp]))
        {
            $Sp++;
            return [
                'type' => 'success',
                'text' => $this->successes[$Sp-1]
            ];
        }

        return false;
    }

}
