<?php


namespace LARAVEL\Controllers\Web;
use Illuminate\Http\Request;
use LARAVEL\Controllers\Controller;
use LARAVEL\Models\CommentModel;
use LARAVEL\Models\GalleryModel;
use LARAVEL\Core\Support\Facades\Func;
use View;
use LARAVEL\Traits\TraitSave;

class CommentController extends Controller
{
    private $errors = [], $result = [], $response = [];
    private $upload;
    use TraitSave;
    public function handle($action, Request $request): void
    {
        match ($action) {
            'add-comment' => $this->addComment($request),
            'reply-comment' => $this->replyComment($request),
            'load-comment' => $this->loadComment($request),
            default => 'unknown',
        };
    }

    public function addComment(Request $request)
    {
        $data = (!empty($request->dataReview)) ? $request->dataReview : null;
        $dataPhoto = Func::listsGallery('review-file-photo');
        if (!empty($data)) {
            foreach ($data as $column => $value) {
                $data[$column] = htmlspecialchars(Func::sanitize($value));
            }
            /* Valid data */
            if (isset($data['star']) && empty($data['star'])) {
                $this->errors[] = 'Chưa chọn đánh giá sao';
            }
            if (isset($data['star']) && !empty($data['star']) && !Func::isNumber($data['star'])) {
                $this->errors[] = 'Đánh giá sao không hợp lệ';
            }
            if (isset($data['title']) && empty($data['title'])) {
                $this->errors[] = 'Chưa nhập tiêu đề đánh giá';
            }
            if (!empty($dataPhoto) && count($dataPhoto) > 3) {
                $this->errors[] = 'Hình ảnh không được vượt quá 3 hình';
            }
            if (empty($this->errors)) {
                $data['date_posted'] = time();
                $itemSave = CommentModel::create($data);
                if (!empty($itemSave)) {
                    $id = $itemSave->id;

                    /* IMAGE */
                    if (!empty($dataPhoto)) {
                        $files = (!empty($request->file('review-file-photo'))) ? $request->file('review-file-photo') : null;
                        $this->insertImges(GalleryModel::class, $request, $files, $id, 'comment', $data['type'], $data['type'], 'photo');
                    }
                }
            }
        } else {
            $this->errors[] = 'Dữ liệu không hợp lệ';
        }
        echo $this->response();
    }

    public function replyComment(Request $request)
    {
        $data = (!empty($request->dataReview)) ? $request->dataReview : null;
        if (!empty($data)) {
            foreach ($data as $column => $value) {
                $data[$column] = htmlspecialchars(Func::sanitize($value));
            }
            /* Valid data */
            if (isset($data['title']) && empty($data['title'])) {
                $this->errors[] = 'Chưa nhập tiêu đề đánh giá';
            }
            if (empty($this->errors)) {
                $data['date_posted'] = time();
                CommentModel::create($data);
            }
        } else {
            $this->errors[] = 'Dữ liệu không hợp lệ';
        }
        echo $this->response();
    }

    public function loadComment(Request $request)
    {
        $data = (!empty($request->dataLoad)) ? $request->dataLoad : null;
        if (!empty($data)) {
            $rowComment = CommentModel::select('*')
                ->where('type', $data['type'])
                ->where('id_parent', 0)
                ->where('id_variant', $data['id'])
                ->skip($data['limit'])->take(2)
                ->get();

            if (($data['limit'] + 2) >= $data['count']) {
                $limit = $data['count'];
                $this->result['pageout'] = true;
            } else {
                $limit = $data['limit'] + 2;
            }
            $this->result['limit'] = $limit;
            $this->result['view'] = View::render('component.comment.loadcomment', ['list' => $rowComment]);
        } else {
            $this->errors[] = 'Dữ liệu không hợp lệ';
        }
        echo $this->response();
    }

    private function response()
    {
        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        } else if (!empty($this->result)) {
            $response['result'] = $this->result;
        } else {
            $response['success'] = true;
        }
        return json_encode($response);
    }
}