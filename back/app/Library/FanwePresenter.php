<?php

namespace App\Library;


use Illuminate\Pagination\BootstrapThreePresenter;

class FanwePresenter extends BootstrapThreePresenter
{


    /**
     * web前端分页的统用样式
     *
     * @return string
     */
    public function render()
    {
        if ($this->hasPages()) {
            return sprintf(
                '<div class="m-page"><div class="page-block"><a href="%s" class="btn-first">首页</a><ul class="pagination">%s %s %s</ul><a href="%s" class="btn-last">尾页</a></div></div>',
                $this->paginator->url(1),
                $this->getPreviousButton(),
                $this->getLinks(),
                $this->getNextButton(),
                $this->paginator->url($this->paginator->lastPage())
            );
        }

        return '';
    }
}
