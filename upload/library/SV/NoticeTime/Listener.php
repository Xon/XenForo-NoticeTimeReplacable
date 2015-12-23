<?php

class SV_NoticeTime_Listener
{
    public static function getRelativeDate(DateTime $now, DateTime $other)
    {
        $interval = $other->diff($now);
        $format = array();
        if ($interval->y)
        {
            if ($interval->y == 1)
            {
                $format[] = '%y year';
            }
            else
            {
                $format[] = '%y years';
            }
        }
        if ($interval->m)
        {
            if ($interval->m == 1)
            {
                $format[] = '%m month';
            }
            else
            {
                $format[] = '%m months';
            }
        }
        if ($interval->d)
        {
            if ($interval->d == 1)
            {
                $format[] = '%d day';
            }
            else
            {
                $format[] = '%d days';
            }
        }
        if ($interval->h)
        {
            if ($interval->h == 1)
            {
                $format[] = '%h hour';
            }
            else
            {
                $format[] = '%h hours';
            }
        }
        if ($interval->i)
        {
            if ($interval->i == 1)
            {
              $format[] = '%i minute';
            }
            else
            {
              $format[] = '%i minutes';
            }
        }
        if ($interval->s == 1)
        {
            $format[] = '%s second';
        }
        else
        {
            $format[] = '%s seconds';
        }
        return $interval->format(join(', ', $format));
    }

    public static function notices_prepare(array &$noticeList, array &$noticeTokens, XenForo_Template_Abstract $template, array $containerData)
    {
        $now = new DateTime();
        $user = XenForo_Visitor::getInstance()->toArray();
        foreach ($noticeList AS $noticeId => &$notice)
        {
            $tokens = array();
            foreach ($notice['user_criteria'] AS &$criteria)
            {
                switch($criteria['rule'])
                {
                    case 'before':
                        $data = $criteria['data'];
                        $datetime = new DateTime("$data[ymd] $data[hh]:$data[mm]",new DateTimeZone(($data['user_tz'] ? $user['timezone'] : $data['timezone'])));
                        $time = XenForo_Locale::dateTime($datetime->getTimestamp(), 'separate');
                        $tokens['{time_end:absolute}'] = $time['date'] . ' ' . $time['time'];
                        $tokens['{time_end:relative}'] = self::getRelativeDate($now, $datetime);
                        $tokens['{time_end}'] = $tokens['{time_end:relative}'];
                        break;
                    case 'after':
                        $data = $criteria['data'];
                        $datetime = new DateTime("$data[ymd] $data[hh]:$data[mm]",new DateTimeZone(($data['user_tz'] ? $user['timezone'] : $data['timezone'])));
                        $time = XenForo_Locale::dateTime($datetime->getTimestamp(), 'separate');
                        $tokens['{time_start:absolute}'] = $time['date'] . ' ' . $time['time'];
                        $tokens['{time_start:relative}'] = self::getRelativeDate($now, $datetime);
                        $tokens['{time_start}'] = $tokens['{time_start:relative}'];
                        break;
                }
            }

            if ($tokens)
            {
                $notice['message'] = str_replace(array_keys($tokens), $tokens, $notice['message']);
            }
        }
    }
}