<?php
namespace App\Helpers;

/**
 * パンくずヘルパークラス
 */
class BreadcrumbHelper
{
    public static function tag($datas, $useHome = true) {
        $tag = "";
        $tag .= "<nav class='flex' aria-label='Breadcrumb'>";
        $tag .= "<ol class='inline-flex items-center space-x-1 md:space-x-3'>";
        if ($useHome) {
            $hr = route('home');
            $tag .= "<li class='inline-flex items-center'>
          <a href='{$hr}' class='inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'>
            <svg class='w-4 h-4 mr-2' fill='currentColor' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z'></path></svg>
            ホーム
          </a>
          </li>";
        }

        for ($i=0; $i < count($datas); $i++) {
            if ($i == count($datas)-1 || !$datas[$i][1]) {
                $tag .= "<li aria-current='page'>
                <div class='flex items-center'>
                  <svg class='w-6 h-6 text-gray-400' fill='currentColor' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path fill-rule='evenodd' d='M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z' clip-rule='evenodd'></path></svg>
                  <span class='ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400'>{$datas[$i][0]}</span>
                </div>
              </li>";
            } else {
                $tag .= "<li>
                    <div class='flex items-center'>
                    <svg class='w-6 h-6 text-gray-400' fill='currentColor' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path fill-rule='evenodd' d='M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z' clip-rule='evenodd'></path></svg>
                    <a href='{$datas[$i][1]}' class='ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white'>{$datas[$i][0]}</a>
                    </div>
                </li>";
            }
        }

        $tag .= "</ol></nav>";
        return $tag;
    }
}