<?php

use Lib\PHPX\Shadcn\Button;

Button::init();

?>

<div class="w-screen h-screen grid place-items-center">
    <div class="flex flex-col gap-2">
        <Button>
            Default
        </Button>
        <Button variant="destructive">
            Destructive
        </Button>
        <Button variant="secondary">
            Secondary
        </Button>
        <Button variant="outline">
            Outline
        </Button>
        <Button variant="ghost">
            Ghost
        </Button>
        <Button variant="link">
            Link
        </Button>
    </div>
</div>