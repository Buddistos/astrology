<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisterController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Validation\ValidatesRequests;

use function App\Http\settings;
use App\Models\Comment;
use App\Models\News;

class AjaxController extends Controller
{
    public function _html(Request $request, $action)
    {

        //--- Поиск Ajax метода для исполнения
        if (method_exists($this, $action)) {
            return $this->$action($request);
        }

        return false;
    }

    //--- Сделать заказ
    public function soon_subscribe($request)
    {
        $out['success'] = 1;
        /**
         * Проверка на человечность
         */

        $humanity = $this->validator($request, 'soon_subscribe');
        if (!$humanity) {
            $out['message'] = 'Сообщение отправлено';

            Log::info("Not sending - validation error");
            Log::info($request->all());

            return json_encode($out);
        }

        $rules = [
            'email' => 'required | email',
        ];
        $messages = [
            'email.required' => 'Поле email обязательно к заполнению',
            'email' => 'Заполните правильно электронную почту',
        ];
        $this->validate($request, $rules, $messages);

        //--- Входные данные
        $data = $request->all();

        //--- Входные файлы
        foreach ($_FILES as $file) {
            $data['files'][] = $file['tmp_name'];
        }

        //--- Отправка письма
        $error = $this->sendMail($data);
        if ($error) {
            $out['error'] = $error;
        }
        $out['message'] = 'Отправлено';

        return json_encode($out);
    }

    //--- Сделать заказ
    public function footer_subscribe($request)
    {
        $out['success'] = 1;
        /**
         * Проверка на человечность
         */

        $humanity = $this->validator($request, 'soon_subscribe');
        if (!$humanity) {
            $out['message'] = 'Сообщение отправлено';

            Log::info("Not sending - validation error");
            Log::info($request->all());

            return json_encode($out);
        }

        $rules = [
            'email' => 'required | email',
        ];
        $messages = [
            'email.required' => 'Поле email обязательно к заполнению',
            'email' => 'Заполните правильно электронную почту',
        ];
        $this->validate($request, $rules, $messages);

        //--- Входные данные
        $data = $request->all();

        //--- Входные файлы
        foreach ($_FILES as $file) {
            $data['files'][] = $file['tmp_name'];
        }

        //--- Отправка письма
        $error = $this->sendMail($data);
        if ($error) {
            $out['error'] = $error;
        }
        $out['message'] = 'Отправлено';

        return json_encode($out);
    }

    public function form_submit($request)
    {
        $out['success'] = 1;
        /**
         * Проверка на человечность
         */
        $humanity = $this->validator($request, 'form_submit');
        if (!$humanity) {
            $out['message'] = '#Сообщение отправлено';
            return json_encode($out);
        }

        /*
         * Валидация отключена - необходимо подключить проверку данных из модуля форм
         *

        $rules = [
            'name' => 'required | max:100',
            'phone' => 'required | phone',
        ];
        $messages = [
            'name.required' => 'Поле имя обязательно к заполнению',
            'phone.required' => 'Поле телефон обязательно к заполнению',
        ];
        $this->validate($request, $rules, $messages);

        */

        //--- Входные данные
        $data = $request->all();

        //--- Входные файлы
        foreach ($_FILES as $file) {
            $data['files'][] = $file['tmp_name'];
        }

        //--- Отправка письма
        //$data['title'] = 'Форма со страницы Контакты';
        $out = $this->sendMail($data);
        return json_encode($out);
    }

    //--- Отправка письма
    public function sendMail($data)
    {
        //--- Отправка письма
        try {
            $mail = new MailController();
            $params['data'] = $data;

            //$form = Form::where('callname', $data['form_callname'])->first();

            $emailto = $data['emailto'] ?? \settings('reply_to') ?? env('MAIL_USERNAME');
            $sendername = \settings('sender_name') ?? $emailto;

            $replyto = $data['email'];
            $toname = $data['name'] ?? $emailto;

            if (!$emailto) {
                throw new \Exception(' email получателя не определён.');
            }

            $mail->sendMail(
                $data['view'] ?? 'forms.letter',
                $params,
                $data['title'],
                $replyto,
                $sendername,
                $emailto,
                $toname
            );

            /*
            Mail::send('site::mail.letter', ['data' => $data], function ($message) use ($data) {
                $mailFrom = isset ($data['email']) ? $data['email'] : 'info@versite.ru';
                $mailTo = settings('email');
                $message->from($mailFrom, $data['name']);
                $message->to($mailTo, 'Mr. Admin')->subject('Форма');

                //--- Подготовка файлов для отправки
                foreach ($_FILES as $file) {
                    if (move_uploaded_file($file['tmp_name'], $file['name'])) {
                        $message->attach($file['name']);
                    }
                }
            });
            */
            $out['error'] = 0;
            $out['message'] = 'Отправлено';

            Log::info("Sending");

            return $out;
        } catch (\Exception $e) {
            $out['error'] = 1;
            $out['message'] = 'Ошибка отправки письма (#2)' . $e->getMessage();

            Log::info("Not sending with error #2");

            return $out;
        }

        //--- Возврат ошибки (false - нет ошибки, письмо отправлено)
        return false;
    }

    public function sendme($request)
    {
        /*
         * Проверка
         */
        $data = $request->all();
        if (isset($data['_email'], $data['_message']) && $data['_token'] != csrf_token()) {
            $validation = 'vs0';
        } else if ($data['_token'] == csrf_token()) {
            $validation = 'vs1';
        }

        /* VerSite Span Code - vssc*/
        $string = $validation . $data['method'] . csrf_token();
        $vssc = bcrypt($string);
        setcookie('vssc', urlencode($vssc), time() + 1000, '/');

        $out['method'] = $data['method'];
        $out['form_callname'] = $data['form_callname'];
        $out['success'] = 1; //fake - только для отвлечения внимания
        return json_encode($out);
    }

    public function validator($data, $method)
    {
        /**
         * Прверяем первую часть: соответствие Versite spam code и Токена
         */
        $vssc = $_COOKIE['vssc'];
        $string = 'vs1' . $method . csrf_token();

        $part1 = \Hash::check($string, urldecode($vssc));

        /**
         * Проверяем вторую часть: наличие заполненных роботом полей
         */
        $part2 = is_null($data['_email']) && is_null($data['_message']);

        return $part1 && $part2;
    }

    public function test()
    {
        return '1234567890';
    }

    public function comment_create($request)
    {
        if (Auth::check()) {
            $demands = [
                'comment' => 'required',
            ];
            $messages = [
                'comment.required' => 'Чтобы отправить сообщение, оставьте сообщение'
            ];
        } else {
            $demands = [
                'author' => 'required|max:255',
                'comment' => 'required',
            ];
            $messages = [
                'author.required' => 'Поле имя обязательно к заполнению',
                'author.max' => 'Имя не может быть больше 255 знаков',
                'comment.required' => 'Чтобы отправить сообщение, оставьте сообщение'
            ];
        }
        $request->validate($demands, $messages);

        $data = $request->input();

        if (Auth::check()) {
            $data['nickname'] = Auth::user()->name;
            $data['user_id'] = Auth::user()->id;
        } else {
            $data['user_id'] = NULL;
            $data['nickname'] = $data['author'];
        }
        $data['group_id'] = $data['comment_parent'];
        $data['parent_id'] = $data['comment_post_ID'];
        $data['name'] = date('Y-m-d H:i', time());
        $comment = Comment::create($data);

        $post = News::find($data['comment_post_ID']);
        $comments = view('blog.show_comments_items')->with([
            'post' => $post
        ])
            ->render();

        return json_encode($comments);
    }

    public function ajax_posts($request)
    {
        if (!isset($request->page)) {
            $out['error'] = 1;
            $out['message'] = 'Ошибка получения записей #1';
            return $out;
        }

        $page = $request->page;

        if (isset($request->category)) {
            $blogposts = News::active()->where('parent_id', $request->category)->orderByDesc('published_at')->get();
        } else if (isset($request->tag)) {
            $tag = Tag::find($request->tag);
            $blogposts = $tag->getPosts()->sortByDesc('published_at');
        } else if (isset($request->search)) {
            $blogposts = News::active()->search($request->search)->get();
        } else {
            $blogposts = News::active()->orderByDesc('published_at')->get();
        }

        $post_on_page = \settings('post_on_page');
        $posts_count = $blogposts->count();
        $posts = $blogposts->forPage($page, $post_on_page);

        $content = view('blog.ajax-posts')->with(['posts' => $posts])->render();

        $out['message'] = $content;
        $out['next'] = ceil($posts_count / $post_on_page);
        return $out;
    }

    public function rateIt($request)
    {
        if (!isset($request->product)) {
            $out['error'] = 1;
            $out['message'] = 'Ошибка запроса #1';
            return $out;
        }

        $product = Product::find($request->product);

        if (!$product->count()) {
            $out['error'] = 1;
            $out['message'] = 'Ошибка запроса #2';
            return $out;
        }

        $rating = $product->rating * $product->rating_count + $request->rate;
        $product->rating_count++;
        $product->rating = $rating / $product->rating_count;
        $product->save();

        setcookie('rate' . $product->id, 1, time() + 60 * 60 * 24 * 30, '/');
        $out['rating'] = $product->rating_count;
        $out['message'] = ' Голос засчитан';
        return $out;
    }

    public function analysquiz($request)
    {
        if (!isset($request->form_id)) {
            $out['error'] = 1;
            $out['message'] = 'Ошибка запроса #1';
            return $out;
        }

        $quiz_id = $request->form_id;
        /**
         * $quiz - Получаем КВИЗ по ID
         */
        $quiz = Quiz::active()->find($quiz_id)->with('questions', 'questions.answers', 'questions.answers.question')->first();

        if (!$quiz->count()) {
            $out['error'] = 1;
            $out['message'] = 'Ошибка запроса #2';
            return $out;
        }

        /**
         * $questions - все вопросы КВИЗа $quiz
         */
        $questions = $quiz->questions;

        /**
         * $quiz_comform - таблица соответствия КВИЗа $quiz
         */
        $quiz_comform = DB::table(str_slug($quiz->slug, '_'));
        $answers_check = $quiz_comform->get();
        $add_messages = collect();
        /**
         * Обработка ответов пользователя
         * $key - слаг вопроса
         * $values - массив ответов пользователя на вопрос $key
         * $result - коллекция из найденных товаров, соответствующих ответам пользователей
         */
        foreach ($request->all() as $key => $values) {

            if (!$values) {
                continue;
            }

            /**
             * $question - текущий вопрос
             * $answers - массив найденных ответов на текущий вопрос
             * $important - важность ответа, нужно ли учитывать ответ при отборе товара
             */
            $question = $questions->where('slug', $key)->first();
            if (is_null($question) || !$question->answers->count()) {
                continue;
            }

            $important = 1;
            $answer = $question->answers->whereIn('slug', $values);

            if (!is_array($values) || count((array)$values) == 1) {
                $important = $answer->first()->important;
            }

            $result = collect();

            foreach ($answers_check as $comform) {
                /**
                 * Проверяем значения ответов в таблице соответствия на принадлежность массиву значений ответов пользователя
                 * Для этого преобразовываем значения в таблице соответствия в массив
                 * $quiz_comform_answer_array
                 */

                $quiz_comform_answer_array = explode(',', $comform->$key);
                if (!count(array_diff((array)$values, $quiz_comform_answer_array)) || !$important) {
                    $product_data[$key] = implode(', ', $answer->pluck('name')->toArray());
                    $result->push($comform);
//                    dump(count(array_diff((array)$values, $quiz_comform_answer_array)));
                }
            }

            /**
             * Формирование дополнительных информационных сообщений при выборе неважных ответов
             */
            if (!$important) {
                $add_messages->push($answer);
            }
            $answers_check = $result;
        }

        if ($answers_check->count() == 1) {
            $product_name = $answers_check->first()->name;
            $product = Product::active()->where('slug', $product_name)->first();
            //$notes = Array();

            foreach ($add_messages as $addition) {
                if ($addition->count() > 1) {
                    continue;
                }

                $add = $addition->first();
                $add_question_slug = $add->question->slug;
                $answer = $answers_check->first();

                if ($add->important || in_array($add->slug, explode(',', $answer->$add_question_slug))) {
                    continue;
                }
                $product->price += $add->price;
                $notes[] = $add->note;
            }
            $product->note = isset($notes) ? $notes : [];
            $result = str_replace('%QUIZPRODUCTFOUND%', $product->h1, $quiz->success);

            $result = str_replace('%QUIZPRODUCTIMAGE%', $product->image, $result);
            $result = preg_replace('~//upload~', '/upload', $result);

            $data_view = '';
            foreach ($product_data as $data_slug => $data) {
                $data_name = $questions->where('slug', $data_slug)->first()->short_name;
                $data_view .= '<i class="fa fa-check" style="color: green;"></i> ' . $data_name . ': <b style="">' . $data . '</b><br>';
            }
            $result = str_replace('%QUIZPRODUCTDATA%', $data_view, $result);

            $result = str_replace('%QUIZPRODUCTADDITION%', implode('<br>', $product->note), $result);

            preg_match('~%QUIZPRODUCTPRICE-(.*)%~', $result, $matches);
            $discount = $matches[1];
            $product->price -= $product->price * $discount / 100;
            $result = str_replace($matches[0], $product->price, $result);

        } elseif ($answers_check->count() > 1) {
            $result = $quiz->onemore;
        } else {
            $result = $quiz->failed;
        }


        return $result;
    }

    //--- Регистрация
    public function registration($request)
    {
        $out['success'] = 1;
        /**
         * Проверка на человечность
         */

        $humanity = $this->validator($request, 'register');
        if (!$humanity) {
            $out['message'] = 'Сообщение отправлено';

            Log::info("Not registrated - validation error");
            Log::info($request->all());

            return json_encode($out);
        }

        $rules = [
            'email' => 'required | email',
            'name' => 'required | text',
            'password' => 'required | email',
        ];
        $messages = [
            'email.required' => 'Поле email обязательно к заполнению',
            'email' => 'Заполните правильно электронную почту',
        ];
        $this->validate($request, $rules, $messages);

        //--- Входные данные
        $data = $request->all();


        //--- Отправка письма
        $error = $this->sendMail($data);
        if ($error) {
            $out['error'] = $error;
        }
        $out['message'] = 'Отправлено';

        return json_encode($out);
    }

    public function carView($request)
    {
        $product = Product::find($request->carid);
        $product->view++;
        $product->save();
        $product->currency = $product->currency()->symbol;
        $product->brand = $product->brand()->name;
        $product->model = $product->model()->name;
        $product->price = getPrice($product->price, 'usd');
        $product->specifications = $product->specifications ? collect(explode("\n", $product->specifications))->take(8) : null;

        $out['cardata'] = view('products.car-ajax', ['product' => $product])->render();

        return $out;
    }
}
