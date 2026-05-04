<?php

class TicketController extends PageController
{
    private array $shows = [
        [
            'id' => 's1',
            'movie' => 'Інтерстеллар',
            'hall' => 'Зал 3',
            'time' => '12:30',
            'price' => 120,
        ],
        [
            'id' => 's2',
            'movie' => 'Титанік',
            'hall' => 'Зал 2',
            'time' => '15:00',
            'price' => 100,
        ],
        [
            'id' => 's3',
            'movie' => 'Темний лицар',
            'hall' => 'Зал 1',
            'time' => '18:00',
            'price' => 80,
        ],
        [
            'id' => 's4',
            'movie' => 'Шрек',
            'hall' => 'Зал 1',
            'time' => '10:00',
            'price' => 50,
        ],
    ];

    private array $seatRows = ['A', 'B', 'C', 'D', 'E'];
    private int $seatsPerRow = 8;

    public function action_booking(): void
    {
        $errors = [];
        $old = [
            'show_id' => '',
            'seats' => [],
        ];

        $reserved = $_SESSION['reserved_seats'] ?? [];

        if ($this->request->isPost()) {
            $old['show_id'] = (string)$this->request->post('show_id', '');
            $old['seats'] = $this->request->post('seat', []);

            $errors = $this->validateBooking($old, $reserved);

            if (empty($errors)) {
                $show = $this->findShow($old['show_id']);
                $selectedSeats = array_values(array_unique($old['seats']));
                $totalPrice = count($selectedSeats) * $show['price'];

                $_SESSION['reserved_seats'][$old['show_id']] = array_values(array_unique(array_merge(
                    $_SESSION['reserved_seats'][$old['show_id']] ?? [],
                    $selectedSeats
                )));

                $_SESSION['last_ticket'] = [
                    'show' => $show,
                    'seats' => $selectedSeats,
                    'total' => $totalPrice,
                    'booked_at' => date('d.m.Y H:i'),
                ];

                $this->redirect('ticket/success');
                return;
            }
        }

        $this->render('ticket/booking', [
            'shows' => $this->shows,
            'seatRows' => $this->seatRows,
            'seatsPerRow' => $this->seatsPerRow,
            'reserved' => $reserved,
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

    private function validateBooking(array $data, array $reserved): array
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

                if ($this->isSeatReserved($data['show_id'], $seat, $reserved)) {
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

        foreach ($this->seatRows as $row) {
            for ($index = 1; $index <= $this->seatsPerRow; $index++) {
                $seats[] = $row . $index;
            }
        }

        return $seats;
    }

    private function isSeatReserved(string $showId, string $seat, array $reserved): bool
    {
        return in_array($seat, $reserved[$showId] ?? [], true);
    }

    private function findShow(string $showId): ?array
    {
        foreach ($this->shows as $show) {
            if ($show['id'] === $showId) {
                return $show;
            }
        }

        return null;
    }
}
