<?php



namespace LARAVEL\Helpers;

use LARAVEL\Core\Singleton;
use LARAVEL\Models\CommentModel;
use LARAVEL\Models\PhotoModel;
use LARAVEL\Models\GalleryModel;
use Func;

class Comment
{
    use Singleton;

    public function countStar($id = 0, $type = '')
    {
        $count = array();

        for ($i = 1; $i <= 5; $i++) {
            $count[$i] = $this->getStar($id, $type, $i);
        }

        return json_encode($count);
    }

    private function getStar($id, $type, $star = 1)
    {
        $row = CommentModel::selectRaw('count(*) as num')
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->where('id_variant', $id)
            ->where('type',  $type)
            ->where('star',  $star)
            ->first();

        return (!empty($row)) ? $row['num'] : 0;
    }

    public function totalByID($id_variant = 0, $type = '', $is_admin = false)
    {
        $query = CommentModel::select('count(id) as num')
            ->where('id_parent', 0)
            ->where('id_variant', $id_variant)
            ->where('type',  $type);
        if (!empty($is_admin)) $query->whereRaw("FIND_IN_SET(?,status)", ['hienthi']);
        $row = $query->orderBy('id', 'desc')
            ->first();

        return (!empty($row)) ? $row['num'] : 0;
    }

    public function newPost($id_variant = 0, $type = '', $status = '')
    {
        $row = CommentModel::select('id_variant')
            ->where('id_variant', $id_variant)
            ->where('type',  $type)
            ->whereRaw("FIND_IN_SET(?,status)", [$status])
            ->orderBy('id', 'desc')
            ->get();

        return (!empty($row)) ? count($row) : 0;
    }

    public function photo($id_parent = 0, $type = '')
    {
        $rows = GalleryModel::select('id', 'photo')
            ->where('type', $type)
            ->where('com', 'comment')
            ->where('id_parent', $id_parent)
            ->get();

        return $rows;
    }

    public function video($id_parent = 0)
    {
        $row = PhotoModel::select('id', 'photo')
            ->where('type', 'comment')
            ->where('com', 'video-comment')
            ->where('id_parent', $id_parent)
            ->first();
        return $row;
    }

    public function perScore($id = 0, $type = '', $num = 1)
    {

        return (!empty($this->total($id, $type))) ? round((json_decode($this->countStar($id, $type), true)[$num] * 100) / $this->total($id, $type), 1) : 0;
    }

    private function totalStar($id = 0, $type = '')
    {
        $row = CommentModel::selectRaw('sum(star) as total_star')
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->where('id_variant', $id)
            ->where('type',  $type)
            ->first();
        return $row['total_star'];
    }

    public function countReply($id = 0, $check = '')
    {
        $query = CommentModel::selectRaw('count(id) as sum')
            ->where('id_parent', $id);
        if (!empty($check)) $query->whereRaw("FIND_IN_SET(?,status)", ['hienthi']);
        if (empty($check)) $query->whereRaw("NOT FIND_IN_SET(?,status)", ['hienthi']);
        $row = $query->first();

        return $row['sum'];
    }

    function total($id = 0, $type = '', $is_admin = false)
    {
        $query = CommentModel::where('id_parent', 0)
            ->where('id_variant', $id)
            ->where('type', $type);

        if (!empty($is_admin)) {
            $query->whereRaw("FIND_IN_SET(?,status)", ['hienthi']);
        }

        return $query->count();
    }

    public function avgPoint($id = 0, $type = '', $is_admin = false)
    {
        return (!empty($this->total($id, $type))) ? round((($this->totalStar($id, $type)) / $this->total($id, $type)), 1) : 0;
    }

    public function avgStar($id = 0, $type = '')
    {
        return (!empty($this->total($id, $type))) ? ($this->totalStar($id, $type) * 100) / ($this->total($id, $type) * 5) : 0;
    }

    public function scoreStar($star = 0)
    {
        return (!empty($star)) ? ($star * 100) / 5 : 0;
    }


    function subName($str)
    {
        $words = Func::changeTitle($str);
        $words = explode(' ', $words);

        $firstLetters = array_map(function ($word) {
            return $word[0];
        }, $words);

        return implode('', $firstLetters);
    }


    public function timeAgo($time = 0)
    {
        $result = '';
        $lang = [
            'now' => 'Vài giây trước',
            'ago' => 'trước',
            'vi' => [
                'y' => 'năm',
                'm' => 'tháng',
                'd' => 'ngày',
                'h' => 'giờ',
                'm' => 'phút',
                's' => 'giây'
            ]
        ];

        $ago = time() - $time;

        if ($ago < 1) {
            $result = $lang['now'];
        } else {
            $unit = [
                365 * 24 * 60 * 60  =>  'y',
                30 * 24 * 60 * 60  =>  'm',
                24 * 60 * 60  =>  'd',
                60 * 60  =>  'h',
                60  =>  'm',
                1  =>  's'
            ];

            foreach ($unit as $secs => $key) {
                $time = $ago / $secs;

                if ($time >= 1) {
                    $time = round($time);
                    $result = $time . ' ' . ($time > 1 ? $lang['vi'][$key] : $lang['vi'][$key]) . ' ' . $lang['ago'];
                    break;
                }
            }
        }

        return $result;
    }
}
