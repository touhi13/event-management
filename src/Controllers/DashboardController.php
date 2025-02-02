<?php

namespace Controllers;

use Core\Auth;
use Core\Controller;
use Models\Attendee;
use Models\Event;
use Models\User;

class DashboardController extends Controller
{
    private Event $eventModel;
    private Attendee $attendeeModel;
    private User $userModel;
    private Auth $auth;

    public function __construct()
    {
        $this->eventModel    = new Event();
        $this->attendeeModel = new Attendee();
        $this->userModel     = new User();
        $this->auth          = Auth::getInstance();
    }

    public function index()
    {
        // Get authenticated user
        $user    = $this->auth->getUser();
        $userId  = $this->auth->getUserId();
        $isAdmin = $this->auth->isAdmin();

        // Get user's events count
        $userEvents  = $this->eventModel->findByUser($userId);
        $eventsCount = count($userEvents);

        // Get user's registrations
        $registrations      = $this->attendeeModel->findAll(['email' => $user['email']]);
        $registrationsCount = count($registrations);

        // Get total users count if admin
        $usersCount = 0;
        if ($isAdmin) {
            $allUsers   = $this->userModel->findAll();
            $usersCount = count($allUsers);
        }

        // Get recent activity (last 5 registrations)
        $recentActivity = array_slice($registrations, 0, 5);
        foreach ($recentActivity as &$activity) {
            $event                  = $this->eventModel->findById($activity['event_id']);
            $activity['event_name'] = $event['name'];
            $activity['event_date'] = $event['event_date'];
        }

        // Render dashboard template with data
        $this->render('dashboard/index', [
            'title'              => 'Dashboard',
            'eventsCount'        => $eventsCount,
            'registrationsCount' => $registrationsCount,
            'usersCount'         => $usersCount,
            'recentActivity'     => $recentActivity,
            'isAdmin'            => $isAdmin,
        ]);
    }
}
