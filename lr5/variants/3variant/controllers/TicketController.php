<?php

class TicketController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_booking(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        $errors = [];
        $old = [
            'show_id' => '',
            'seats' => [],
        ];

        if ($this->request->isPost()) {
            $old['show_id'] = (string)$this->request->post('show_id', '');
            $old['seats'] = $this->request->post('seat', []);

            $errors = $this->validateBooking($old);

            if (empty($errors)) {
                $show = $this->findShow($old['show_id']);
                $selectedSeats = array_values(array_unique($old['seats']));
                $totalPrice = count($selectedSeats) * $show['price'];

                $this->db->beginTransaction();
                try {
                    $stmt = $this->db->prepare(
                        'INSERT INTO reservations (show_id, user_id, seat) VALUES (:show_id, :user_id, :seat)'
                    );

                    foreach ($selectedSeats as $seat) {
                        $stmt->execute([
                            ':show_id' => $old['show_id'],
                            ':user_id' => $_SESSION['user_id'],
                            ':seat' => $seat,
                        ]);
                    }

                    $this->db->commit();

                    $_SESSION['last_ticket'] = [
                        'show' => $show,
                        'seats' => $selectedSeats,
                        'total' => $totalPrice,
                        'booked_at' => date('d.m.Y H:i'),
                    ];

                    $this->redirect('ticket/success');
                    return;
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $errors['seats'] = 'Помилка бронювання. Спробуйте ще раз.';
                }
            }
        }

        $shows = $this->getShowsWithMovies();
        $reservedSeats = $this->getReservedSeats();

        $this->render('ticket/booking', [
            'shows' => $shows,
            'seatRows' => ['A', 'B', 'C', 'D', 'E'],
            'seatsPerRow' => 8,
            'reserved' => $reservedSeats,
            'errors' => $errors,
            'old' => $old,
        ], 'Бронювання квитків');
    }

    public function action_success(): void
    {
        if (empty($_SESSION['last_ticket'])) {
            $this->redirect('ticket/booking');
            return;
        }

        $ticket = $_SESSION['last_ticket'];
        unset($_SESSION['last_ticket']);

        $this->render('ticket/success', [
            'ticket' => $ticket,
        ], 'Квиток придбано');
    }

    private function validateBooking(array $data): array
    {
        $errors = [];

        $show = $this->findShow($data['show_id']);
        if ($show === null) {
            $errors['show_id'] = 'Оберіть сеанс.';
        }

        if (!is_array($data['seats']) || count($data['seats']) === 0) {
            $errors['seats'] = 'Оберіть хоча б одне місце.';
        }

        if (empty($errors)) {
            $validSeats = $this->generateSeatMap();
            $chosenSeats = array_values(array_unique($data['seats']));

            foreach ($chosenSeats as $seat) {
                if (!in_array($seat, $validSeats, true)) {
                    $errors['seats'] = 'Оберіть коректне місце.';
                    break;
                }

                if ($this->isSeatReserved($data['show_id'], $seat)) {
                    $errors['seats'] = "Місце {$seat} вже заброньовано. Оберіть інше.";
                    break;
                }
            }
        }

        return $errors;
    }

    private function generateSeatMap(): array
    {
        $seats = [];
        $rows = ['A', 'B', 'C', 'D', 'E'];

        foreach ($rows as $row) {
            for ($index = 1; $index <= 8; $index++) {
                $seats[] = $row . $index;
            }
        }

        return $seats;
    }

    private function isSeatReserved(string $showId, string $seat): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM reservations WHERE show_id = :show_id AND seat = :seat');
        $stmt->execute([':show_id' => $showId, ':seat' => $seat]);
        return $stmt->fetchColumn() > 0;
    }

    private function findShow(string $showId): ?array
    {
        $stmt = $this->db->prepare('
            SELECT s.*, m.title as movie_title, m.director, m.genre, m.year, m.duration_min
            FROM shows s
            JOIN movies m ON s.movie_id = m.id
            WHERE s.id = :id
        ');
        $stmt->execute([':id' => $showId]);
        return $stmt->fetch() ?: null;
    }

    private function getShowsWithMovies(): array
    {
        $stmt = $this->db->query('
            SELECT s.*, m.title as movie_title, m.director, m.genre, m.year, m.duration_min
            FROM shows s
            JOIN movies m ON s.movie_id = m.id
            ORDER BY s.show_time
        ');
        return $stmt->fetchAll();
    }

    private function getReservedSeats(): array
    {
        $stmt = $this->db->query('SELECT show_id, seat FROM reservations');
        $reservations = $stmt->fetchAll();

        $reserved = [];
        foreach ($reservations as $reservation) {
            $reserved[$reservation['show_id']][] = $reservation['seat'];
        }

        return $reserved;
    }
}