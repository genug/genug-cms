<?php

use genug\Http\ContentType;
use genug\Http\GenericResponce;
use genug\Page\GetPageDto;
use genug\Http\Response;
use genug\Http\Status;
use genug\Page\IsPageEntity;
use genug\Page\PageEntity;

return new class () implements PageEntity {
    use IsPageEntity;

    private string $title;
    private string $text;

    #[Override]
    public function init(): void
    {
        $this->title = 'lorem';
        $this->text = 'ipsum';
    }

    #[Override]
    public function get(GetPageDto $dto): Response
    {
        $dom = \Dom\HTMLDocument::createFromString("<title>{$this->title}</title><p>{$this->text}</p>");

        return new GenericResponce(Status::OK, ContentType::HTML, $dom->saveHtml());
    }
};