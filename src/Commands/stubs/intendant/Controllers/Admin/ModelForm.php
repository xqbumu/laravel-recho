<?php

namespace Intendant\{$stub_intendant_zone_upper}\Controllers\Incore;

trait ModelForm
{
    public function show($id)
    {
        return $this->edit($id);
    }

    public function update($id)
    {
        return $this->form()->update($id);
    }

    public function destroy($id)
    {
        if ($this->form()->destroy($id)) {
            return response()->json([
                'status'  => true,
                'message' => trans('docore::lang.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('docore::lang.delete_failed'),
            ]);
        }
    }

    public function store()
    {
        return $this->form()->store();
    }
}
