<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'new_clock_in'  => ['required'],
            'new_clock_out' => ['required'],
            'new_note'      => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'new_clock_in.required'  => '出勤時間を入力してください。',
            'new_clock_out.required' => '退勤時間を入力してください。',
            'new_note.required'      => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $date = $this->route('id') 
                ? Attendance::findOrFail($this->route('id'))->clock_in->format('Y-m-d')
                : now()->format('Y-m-d');

            $clockIn = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $this->input('new_clock_in'));
            $clockOut = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $this->input('new_clock_out'));

            if ($clockIn->gt($clockOut)) {
                $validator->errors()->add('new_clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            $breaks = array_merge(
                $this->input('new_breaks', []),
                $this->input('new_breaks_add', [])
            );

            foreach ($breaks as $break) {
                $start = $break['start_time'] ?? null;
                $end = $break['end_time'] ?? null;
                if ($start && !$end) {
                    $validator->errors()->add('breaks', '休憩終了時間を入力してください');
                }
                if ($end && !$start) {
                    $validator->errors()->add('breaks', '休憩開始時間を入力してください');
                }
                if ($start && $end) {
                    $startTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $start);
                    $endTime   = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $end);

                    if ($startTime->lt($clockIn) || $endTime->gt($clockOut)) {
                        $validator->errors()->add('breaks', '休憩時間が勤務時間外です');
                        break;
                    }
                    if ($startTime->gt($endTime)) {
                        $validator->errors()->add('breaks', '休憩の開始時間と終了時間が不正です');
                        continue;
                    }
                }
            }
        });
    }
}
