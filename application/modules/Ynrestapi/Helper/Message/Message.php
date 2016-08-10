<?php

class Ynrestapi_Helper_Message_Message extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('message', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->message_id;
    }

    public function field_title()
    {
        $message = $this->entry;
        $conversation = Engine_Api::_()->getItem('messages_conversation', $message->conversation_id);

        (!isset($conversation) && '' != ($title = trim($conversation->getTitle())) ||
            !(isset($message) && '' != ($title = trim($message->getTitle())) ||
                $title = $this->view->translate('(No Subject)')));

        $this->data['title'] = $title;
    }

    public function field_from()
    {
        $message = $this->entry;
        $user = Engine_Api::_()->user()->getUser($message->user_id);

        $from = array(
            'id' => $message->user_id,
            'title' => $user->getTitle(),
            'img' => $this->itemPhoto($user, 'thumb.icon'),
        );

        $this->data['from'] = $from;
    }

    public function field_date()
    {
        $this->data['date'] = $this->entry->date;
    }

    public function field_body()
    {
        $this->data['body'] = nl2br(html_entity_decode($this->entry->body));
    }

    public function field_attachment()
    {
        $rs = null;
        $message = $this->entry;

        if (!empty($message->attachment_type) && null !== ($attachment = $this->view->item($message->attachment_type, $message->attachment_id))) {
            $rs = array(
                'id' => $message->attachment_id,
                'type' => $message->attachment_type,
                'title' => $attachment->getTitle(),
                'description' => $attachment->getDescription(),
                'img' => $this->itemPhoto($attachment, 'thumb.normal'),
            );

            switch ($message->attachment_type) {
                case 'album_photo':
                    $rs['src'] = Ynrestapi_Helper_Utils::prepareUrl($attachment->getPhotoUrl());
                    break;

                case 'music_playlist_song':
                    $rs['src'] = Ynrestapi_Helper_Utils::prepareUrl($attachment->getFilePath());
                    break;

                case 'video':
                    // if video type is youtube
                    if ($attachment->type == 1) {
                        $rs['src'] = 'https://www.youtube.com/embed/' . $attachment->code;
                    }
                    // if video type is vimeo
                    if ($attachment->type == 2) {
                        $rs['src'] = 'https://player.vimeo.com/video/' . $attachment->code;
                    }

                    // if video type is uploaded
                    if ($attachment->type == 3) {
                        $storage_file = Engine_Api::_()->storage()->get($attachment->file_id, $attachment->getType());
                        $video_location = $storage_file->getHref();
                        $rs['src'] = Ynrestapi_Helper_Utils::prepareUrl($video_location);
                    }
                    break;

                default:
                    $rs['src'] = Ynrestapi_Helper_Utils::prepareUrl($attachment->getHref(array('message' => $message->conversation_id)));
                    break;
            }
        }

        $this->data['attachment'] = $rs;
    }

    public function field_conversation()
    {
        $message = $this->entry;
        $conversation = Engine_Api::_()->getItem('messages_conversation', $message->conversation_id);

        $this->data['conversation'] = Ynrestapi_Helper_Meta::exportOne($conversation, array('listing'));
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_from();
        $this->field_date();
        $this->field_body();
        $this->field_attachment();
    }

    public function field_search()
    {
        $this->field_id();
        $this->field_title();
        $this->field_date();
        $this->field_body();
        $this->field_conversation();
    }
}
