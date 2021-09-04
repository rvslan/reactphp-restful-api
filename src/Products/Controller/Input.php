<?php

namespace App\Products\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;

final class Input
{
    private $request;

    private const SUPPORTED_FILE_TYPES = ['image/jpg', 'image/png'];

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function validate(): void
    {
        $this->validateFields();
        $this->validateUploadedFile();
    }

    public function name(): string
    {
        return $this->request->getParsedBody()['name'];
    }

    public function price(): float
    {
        return (float) $this->request->getParsedBody()['price'];
    }

    public function image(): ?UploadedFileInterface
    {
        $files = $this->request->getUploadedFiles();

        return $files['image'] ?? null;
    }

    private function validateFields(): void
    {
        $nameValidator = Validator::key(
            'name',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )
        )->setName('name');

        $priceValidator = Validator::key(
            'price',
            Validator::allOf(
                Validator::number(),
                Validator::positive(),
                Validator::notBlank()
            )
        )->setName('price');

        Validator::allOf($nameValidator, $priceValidator)
            ->assert(
                $this->request->getParsedBody()
            );
    }

    private function validateUploadedFile(): void
    {
        if ($this->image() === null) {
            return;
        }

        if (!in_array($this->image()->getClientMediaType(), self::SUPPORTED_FILE_TYPES)) {
            throw new NestedValidationException(
                'Image has invalid extension. Select jpg or png file.'
            );
        }
    }
}
