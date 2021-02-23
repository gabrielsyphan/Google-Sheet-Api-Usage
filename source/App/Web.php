<?php

namespace Source\App;

use League\Plates\Engine;
use Stonks\Router\Router;
use Source\Models\Staff;
use Source\Models\Status;
use Source\Models\Ticket;
use Source\Models\Team;
use Source\Models\Db;
use Hybridauth\Provider\Google;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;
use GuzzleHttp\Client;

class Web
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Engine
     */
    private $view;
    private $service;

    /**
     * Web constructor.
     */
    public function __construct($router)
    {
        $this->router = $router;
        $this->view = Engine::create(THEMES, 'php');
        $this->view->addData([
            'router' => $router,
        ]);

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
    }

    /**
     * @return void
     */
    public function home(): void
    {
        $db = new Db();
        $agents = (new Staff())->find('', '', 'staff_id, firstname, lastname')->fetch(true);
        $teams = (new Team())->find('', '', 'team_id, name')->fetch(true);
        
        echo $this->view->render("home", [
            "title" => "Site do Paulo",
            "tickets" => null,
            "date" => "null",
            "aux" => true,
            "sheets" => $db->get_sheets(),
            "agents" => $agents,
            "teams" => $teams
        ]);
    }
    
    public function signout(): void 
    {
        $db = new Db();
        $db->delete_token();
        
        echo "
        <script>
            alert('Você está deslogado.');
            window.location.href = 'https://localhost/sheetsApi';
        </script>";
    }

    public function sheet(): void
    {
        $config = [
            'callback' => 'https://localhost/sheetsApi/sheet',
            'keys' => [
                'id' => GOOGLE_CLIENT_ID,
                'secret' => GOOGLE_CLIENT_SECRET
            ],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'authorize_url_parameters' => [
                'approval_prompt' => 'force',
                'access_type' => 'offline'
            ]
        ];

        $adapter = new Google($config);

        try {
            $adapter->authenticate();
            $token = $adapter->getAccessToken();
            $db = new DB();
            if ($db->is_table_empty()) {
                $db->update_access_token(json_encode($token));
                echo "Access token inserted successfully.";
            }
            
            $this->router->redirect("web.home");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function createSheet($data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        
        $config = [
            'callback' => 'https://localhost/sheetsApi/sheet',
            'keys' => [
                'id' => GOOGLE_CLIENT_ID,
                'secret' => GOOGLE_CLIENT_SECRET
            ],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'authorize_url_parameters' => [
                'approval_prompt' => 'force',
                'access_type' => 'offline'
            ]
        ];

        $adapter = new Google($config);

        $client = new Google_Client();

        $db = new DB();

        $arr_token = (array)$db->get_access_token();
        $accessToken = array(
            'access_token' => $arr_token['access_token'],
            'expires_in' => $arr_token['expires_in'],
        );

        $client->setAccessToken($accessToken);

        $service = new Google_Service_Sheets($client);

        try {
            $spreadsheet = new Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => $data['sheetName']
                ]
            ]);
            $spreadsheet = $service->spreadsheets->create($spreadsheet, [
                'fields' => 'spreadsheetId'
            ]);

            $db->new_sheet($spreadsheet->spreadsheetId, $data['sheetName']);
            echo "
            <script>
                alert('Plhanilha criada com sucesso! Link: https://docs.google.com/spreadsheets/d/". $spreadsheet->spreadsheetId ."');
                window.location.href = 'https://localhost/sheetsApi';
            </script>";
        } catch (Exception $e) {
            if (401 == $e->getCode()) {
                $refresh_token = $db->get_refersh_token();

                $client = new Client(['base_uri' => 'https://accounts.google.com']);

                $response = $client->request('POST', '/o/oauth2/token', [
                    'form_params' => [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $refresh_token,
                        "client_id" => GOOGLE_CLIENT_ID,
                        "client_secret" => GOOGLE_CLIENT_SECRET,
                    ],
                ]);

                $data = (array)json_decode($response->getBody());
                $data['refresh_token'] = $refresh_token;

                $db->update_access_token(json_encode($data));

                $this->createSheet();
            } else {
                echo $e->getMessage();
            }
        }
    }

    public function writeSheet($sheetId): void
    {
        $config = [
            'callback' => 'https://localhost/sheetsApi/sheet',
            'keys' => [
                'id' => GOOGLE_CLIENT_ID,
                'secret' => GOOGLE_CLIENT_SECRET
            ],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'authorize_url_parameters' => [
                'approval_prompt' => 'force',
                'access_type' => 'offline'
            ]
        ];

        $spreadsheetId = $sheetId;

        $client = new Google_Client();

        $db = new DB();

        $arr_token = (array)$db->get_access_token();
        $accessToken = array(
            'access_token' => $arr_token['access_token'],
            'expires_in' => $arr_token['expires_in'],
        );

        $client->setAccessToken($accessToken);

        $service = new Google_Service_Sheets($client);

        try {
            $range = 'A1:F1';
            $values = [
                [
                    'Número do ticket',
                    'Data de criação',
                    'Equipe',
                    'Agente',
                    'Status',
                    'Atrasado'
                ],
            ];
            $body = new Google_Service_Sheets_ValueRange([
                'values' => $values
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
        } catch (Exception $e) {
            if (401 == $e->getCode()) {
                $refresh_token = $db->get_refersh_token();

                $client = new Client(['base_uri' => 'https://accounts.google.com']);

                $response = $client->request('POST', '/o/oauth2/token', [
                    'form_params' => [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $refresh_token,
                        "client_id" => GOOGLE_CLIENT_ID,
                        "client_secret" => GOOGLE_CLIENT_SECRET,
                    ],
                ]);

                $data = (array)json_decode($response->getBody());
                $data['refresh_token'] = $refresh_token;

                $db->update_access_token(json_encode($data));

                write_to_sheet($spreadsheetId);
            } else {
                echo $e->getMessage();
            }
        }
    }

    public function appendSheet($tickets, $sheetId): void
    {
        $this->writeSheet($sheetId);
        $config = [
            'callback' => 'https://localhost/sheetsApi/sheet',
            'keys' => [
                'id' => GOOGLE_CLIENT_ID,
                'secret' => GOOGLE_CLIENT_SECRET
            ],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'authorize_url_parameters' => [
                'approval_prompt' => 'force',
                'access_type' => 'offline'
            ]
        ];

        $spreadsheetId = $sheetId;

        $client = new Google_Client();

        $db = new DB();

        $arr_token = (array) $db->get_access_token();
        $accessToken = array(
            'access_token' => $arr_token['access_token'],
            'expires_in' => $arr_token['expires_in'],
        );

        $client->setAccessToken($accessToken);

        $service = new Google_Service_Sheets($client);

        try {
            $range = 'A1:1';
            $values = [];
            if ($tickets) {
                foreach ($tickets as $ticket) {
                    $team = (new Team)->findById($ticket->team_id, 'name');
                    $agent = (new Staff)->findById($ticket->staff_id, 'firstname, lastname');
                    $status = (new Status)->findById($ticket->status_id, 'name');
    
                    $ticket->team = $team->name;
                    $ticket->staff = $agent->firstname . ' ' . $agent->lastname;
                    if($status->name) {
                        $ticket->status = $status->name;   
                    } else {
                        $ticket->status = "";
                    }
                    
                    if ($ticket->isoverdue == 0) {
                        $overdue = "Não";
                    } else {
                        $overdue = "Sim";
                    }
                    
                    $values[] = 
                    [
                        "". $ticket->number,
                        "". $ticket->created,
                        "". $ticket->team,
                        "". $ticket->staff,
                        "". $ticket->status,
                        "". $overdue
                        
                    ];
                }
            }

            $body = new Google_Service_Sheets_ValueRange([
                'values' => $values
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            
            $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
            echo "
            <script>
                alert('Dados exportados com sucesso!');
                window.location.href = 'https://localhost/sheetsApi';
            </script>";
        } catch(Exception $e) {
            if( 401 == $e->getCode() ) {
                $refresh_token = $db->get_refersh_token();

                $client = new GuzzleHttp\Client(['base_uri' => 'https://accounts.google.com']);

                $response = $client->request('POST', '/o/oauth2/token', [
                    'form_params' => [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $refresh_token,
                        "client_id" => GOOGLE_CLIENT_ID,
                        "client_secret" => GOOGLE_CLIENT_SECRET,
                    ],
                ]);

                $data = (array) json_decode($response->getBody());
                $data['refresh_token'] = $refresh_token;

                $db->update_access_token(json_encode($data));

                append_to_sheet($spreadsheetId);
            } else {
                echo $e->getMessage(); //print the error just in case your video is not uploaded.
            }
        }
    }

    /**
     * @return void
     */
    public function filter($data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        
        $db = new Db;
        
        if($data["agent"] == 0 && $data["team"] == 0) {
            $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($data['data']))) . '"', 'date=' . $data['data'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
        } else if ($data["agent"] != 0 && $data["team"] == 0) {
            $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($data['data']))) . '" AND staff_id = :staffId', 'date=' . $data['data'] . '&staffId=' . $data['agent'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
        } else if ($data["agent"] == 0 && $data["team"] != 0) {
            $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($data['data']))) . '" AND team_id = :steamId', 'date=' . $data['data'] . '&steamId=' . $data['team'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
        }
        
        $agents = (new Staff())->find('', '', 'staff_id, firstname, lastname')->fetch(true);
        $teams = (new Team())->find('', '', 'team_id, name')->fetch(true);
    

        if ($tickets) {
            foreach ($tickets as $ticket) {
                $team = (new Team)->findById($ticket->team_id, 'name');
                $agent = (new Staff)->findById($ticket->staff_id, 'firstname, lastname');
                $status = (new Status)->findById($ticket->status_id, 'name');

                $ticket->team = $team->name;
                $ticket->staff = $agent->firstname . ' ' . $agent->lastname;
                $ticket->status = $status->name;
            }
        }

        echo $this->view->render("home", [
            "title" => "Site do Paulo",
            "data" => $data,
            "tickets" => $tickets,
            "date" => $data['data'],
            "sheets" => $db->get_sheets(),
            "agents" => $agents,
            "teams" => $teams,
            "data" => $data
        ]);
    }

    /**
     * @param array $data
     * @return void
     */
    public function error(array $data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        echo $this->view->render('error', [
            'title' => "Erro {$data['errcode']} | " . SITE,
            'error' => $data['errcode'],
        ]);
    }
    
    public function date(array $data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        
        $db = new Db;

        if($data["agent"] == 0 && $data["team"] == 0) {
            $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($data['date2']))) . '"', 'date=' . $data['date1'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
        } else if ($data["agent"] != 0 && $data["team"] == 0) {
            $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($data['date2']))) . '" AND staff_id = :staffId', 'date=' . $data['date1'] . '&staffId=' . $data['agent'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
        } else if ($data["agent"] == 0 && $data["team"] != 0) {
            $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($data['date2']))) . '" AND team_id = :steamId', 'date=' . $data['date1'] . '&steamId=' . $data['team'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
        }
        
        $agents = (new Staff())->find('', '', 'staff_id, firstname, lastname')->fetch(true);
        $teams = (new Team())->find('', '', 'team_id, name')->fetch(true);
        
        if ($tickets) {
            foreach ($tickets as $ticket) {
                $team = (new Team)->findById($ticket->team_id, 'name');
                $agent = (new Staff)->findById($ticket->staff_id, 'firstname, lastname');
                $status = (new Status)->findById($ticket->status_id, 'name');

                $ticket->team = $team->name;
                $ticket->staff = $agent->firstname . ' ' . $agent->lastname;
                $ticket->status = $status->name;
            }
        }
        
        echo $this->view->render("home", [
            "title" => "Site do Paulo",
            "tickets" => $tickets,
            "date" => $data['date1'] . ',' . $data['date2'],
            "sheets" => $db->get_sheets(),
            "agents" => $agents,
            "teams" => $teams,
            "data" => $data
        ]);
    }

    /**
     * @return void
     * Export payment list in xls file
     */
    public function exportData($data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $date = explode(",", $data['data']);
        if(isset($date[1])) {
            if($data["agent"] == 0 && $data["team"] == 0) {
                $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($date[1]))) . '"', 'date=' . $date[0], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
            } else if ($data["agent"] != 0 && $data["team"] == 0) {
                $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($date[1]))) . '" AND staff_id = :staffId', 'date=' . $date[0] . '&staffId=' . $data['agent'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
            } else if ($data["agent"] == 0 && $data["team"] != 0) {
                $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($date[1]))) . '" AND team_id = :steamId', 'date=' . $date[0] . '&steamId=' . $data['team'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
            }
        } else {
            if($data["agent"] == 0 && $data["team"] == 0) {
                $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($data['data']))) . '"', 'date=' . $data['data'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
            } else if ($data["agent"] != 0 && $data["team"] == 0) {
                $tickets = (new Ticket())->find('created > :date AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($data['data']))) . '" AND staff_id = :staffId', 'date=' . $data['data']  . '&staffId=' . $data['agent'], 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
            } else if ($data["agent"] == 0 && $data["team"] != 0) {
                $tickets = (new Ticket())->find('created > "'. $date[0] .'" AND created < "' . date('Y-m-d', strtotime(' +1 days', strtotime($date[1]))) . '" AND team_id = :steamId' . '&steamId=' . $data['team'], '', 'number, created, team_id, staff_id, status_id, isoverdue')->fetch(true);
            }
        }
        
        $this->appendSheet($tickets, $data['sheetId']);
    }
}
