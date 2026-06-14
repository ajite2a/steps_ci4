<?php

namespace App\Controllers;

use App\Libraries\AccessDB;

class Sync extends BaseController
{
    protected $accessDB;

    public function __construct()
    {
        $this->accessDB = AccessDB::getInstance();
    }

    public function index()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $filter = $this->request->getGet('filter') ?? 'pending';
        $logs = ($filter === 'all')
            ? $this->accessDB->getAllChangeLogs()
            : $this->accessDB->getPendingChangeLogs();

        $data = [
            'logs'       => $logs,
            'filter'     => $filter,
            'user_name'  => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'pageTitle'  => 'Pending Sync',
        ];

        return view('sync/index', $data);
    }

    public function markSynced($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $ok = $this->accessDB->markChangeLogSynced((int) $id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => $ok]);
        }

        if ($ok) {
            $session->setFlashdata('success', 'Record marked as synced.');
        } else {
            $session->setFlashdata('error', 'Failed to mark record as synced.');
        }

        return redirect()->route('sync.index');
    }

    public function markAllSynced()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->route('login');
        }

        $ok = $this->accessDB->markAllChangeLogsSynced();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => $ok]);
        }

        if ($ok) {
            $session->setFlashdata('success', 'All pending records marked as synced.');
        } else {
            $session->setFlashdata('error', 'Failed to mark records as synced.');
        }

        return redirect()->route('sync.index');
    }
}
