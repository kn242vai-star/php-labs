<?php

class UploadController extends PageController
{
    private string $uploadDir;
    private array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private int $maxSize = 5 * 1024 * 1024; // 5 MB
    private bool $canCheckMime;

    public function __construct()
    {
        parent::__construct();
        $this->uploadDir = DATA_DIR . '/uploads';
        $this->canCheckMime = class_exists('finfo') || function_exists('mime_content_type') || function_exists('getimagesize');

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function action_index(): void
    {
        $message = '';
        $error = '';

        if ($this->request->isPost() && isset($_FILES['image'])) {
            $file = $_FILES['image'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Помилка завантаження файлу (код: ' . $file['error'] . ').';
            } elseif ($file['size'] > $this->maxSize) {
                $error = 'Максимальний розмір файлу: 5 МБ.';
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $realType = null;

                if (!in_array($ext, $this->allowedExtensions, true)) {
                    $error = 'Дозволені формати: JPEG, PNG, GIF, WebP.';
                } else {
                    if ($this->canCheckMime) {
                        if (class_exists('finfo')) {
                            $finfo = new finfo(FILEINFO_MIME_TYPE);
                            $realType = $finfo->file($file['tmp_name']);
                        } elseif (function_exists('mime_content_type')) {
                            $realType = mime_content_type($file['tmp_name']);
                        } elseif (function_exists('getimagesize')) {
                            $info = getimagesize($file['tmp_name']);
                            $realType = $info[2] ? image_type_to_mime_type($info[2]) : null;
                        }
                    }

                    if ($realType !== null && !in_array($realType, $this->allowedMimeTypes, true)) {
                        $error = 'Дозволені формати: JPEG, PNG, GIF, WebP.';
                    }
                }
            }
            if ($error === '' && isset($ext)) {
                $safeName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = $this->uploadDir . '/' . $safeName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $message = 'Зображення "' . htmlspecialchars($file['name']) . '" завантажено!';
                } else {
                    $error = 'Не вдалося зберегти файл.';
                }
            }
        }

        $images = $this->getImages();

        $this->render('upload/index', [
            'images' => $images,
            'message' => $message,
            'error' => $error,
        ], 'Завантаження зображень');
    }

    public function action_delete(): void
    {
        if ($this->request->isPost()) {
            $image = basename($this->request->post('image') ?? '');
            $target = $this->uploadDir . '/' . $image;

            if ($image === '' || !in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), $this->allowedExtensions, true)) {
                $_SESSION['flash_error'] = 'Некоректна назва зображення.';
            } elseif (!is_file($target)) {
                $_SESSION['flash_error'] = 'Файл не знайдено.';
            } elseif (!unlink($target)) {
                $_SESSION['flash_error'] = 'Не вдалося видалити зображення.';
            } else {
                $_SESSION['flash_success'] = 'Зображення видалено.';
            }
        }

        $this->redirect('upload/index');
    }

    public function action_rename(): void
    {
        if ($this->request->isPost()) {
            $currentName = basename($this->request->post('current_name') ?? '');
            $newNameRaw = trim($this->request->post('new_name') ?? '');
            $currentPath = $this->uploadDir . '/' . $currentName;
            $oldExt = strtolower(pathinfo($currentName, PATHINFO_EXTENSION));

            if ($currentName === '' || !is_file($currentPath)) {
                $_SESSION['flash_error'] = 'Файл не знайдено.';
            } elseif ($newNameRaw === '') {
                $_SESSION['flash_error'] = 'Вкажіть нову назву файлу.';
            } else {
                $newBase = basename($newNameRaw);
                $newExt = strtolower(pathinfo($newBase, PATHINFO_EXTENSION));

                if ($newExt === '') {
                    $newBase .= '.' . $oldExt;
                    $newExt = $oldExt;
                }

                if (!in_array($newExt, $this->allowedExtensions, true)) {
                    $_SESSION['flash_error'] = 'Розширення файлу має бути JPEG, PNG, GIF або WebP.';
                } elseif ($newExt !== $oldExt) {
                    $_SESSION['flash_error'] = 'Змінювати розширення файлу не можна.';
                } else {
                    $newBase = preg_replace('/[^\p{L}\p{N}_\-\. ]/u', '_', $newBase);
                    $newBase = preg_replace('/\s+/', ' ', $newBase);
                    if ($newBase === $currentName) {
                        $_SESSION['flash_error'] = 'Нова назва така сама, як існуюча.';
                    } else {
                        $newPath = $this->uploadDir . '/' . $newBase;
                        if (is_file($newPath)) {
                            $_SESSION['flash_error'] = 'Файл з такою назвою вже існує.';
                        } elseif (!rename($currentPath, $newPath)) {
                            $_SESSION['flash_error'] = 'Не вдалося перейменувати файл.';
                        } else {
                            $_SESSION['flash_success'] = 'Зображення перейменовано.';
                        }
                    }
                }
            }
        }

        $this->redirect('upload/index');
    }

    private function getImages(): array
    {
        $images = [];
        $files = glob($this->uploadDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

        if ($files) {
            rsort($files);
            foreach ($files as $file) {
                $baseName = basename($file);
                $images[] = [
                    'name' => $baseName,
                    'display_name' => pathinfo($baseName, PATHINFO_FILENAME),
                    'extension' => strtolower(pathinfo($baseName, PATHINFO_EXTENSION)),
                    'url' => 'data/uploads/' . $baseName,
                    'size' => filesize($file),
                    'date' => date('Y-m-d H:i', filemtime($file)),
                ];
            }
        }

        return $images;
    }
}
