<?php

return [

    'default' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.sentence',
        'rules' => 'stubkit::rules.string',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.text',
        'edit' => 'stubkit::edit.text',
    ],

    'string' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.sentence',
        'rules' => 'stubkit::rules.string',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.text',
        'edit' => 'stubkit::edit.text',
    ],

    'text' => [
        'schema' => 'stubkit::schema.text',
        'faker' => 'stubkit::faker.paragraph',
        'rules' => 'stubkit::rules.text',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.textarea',
        'edit' => 'stubkit::edit.textarea',
    ],

    'number' => [
        'schema' => 'stubkit::schema.integer',
        'faker' => 'stubkit::faker.number',
        'rules' => 'stubkit::rules.number',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.number',
        'edit' => 'stubkit::edit.number',
    ],

    'decimal' => [
        'schema' => 'stubkit::schema.decimal',
        'faker' => 'stubkit::faker.decimal',
        'rules' => 'stubkit::rules.number',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.decimal',
        'edit' => 'stubkit::edit.decimal',
    ],

    'boolean' => [
        'schema' => 'stubkit::schema.boolean',
        'faker' => 'stubkit::faker.boolean',
        'rules' => 'stubkit::rules.boolean',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.checkbox',
        'edit' => 'stubkit::edit.checkbox',
    ],

    'date' => [
        'schema' => 'stubkit::schema.date',
        'faker' => 'stubkit::faker.date',
        'rules' => 'stubkit::rules.date',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.date',
        'edit' => 'stubkit::edit.date',
    ],

    'time' => [
        'schema' => 'stubkit::schema.time',
        'faker' => 'stubkit::faker.time',
        'rules' => 'stubkit::rules.time',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.time',
        'edit' => 'stubkit::edit.time',
    ],

    'timestamp' => [
        'schema' => 'stubkit::schema.timestamp',
        'faker' => 'stubkit::faker.datetime',
        'rules' => 'stubkit::rules.date',
        'index' => 'stubkit::index.timestamp',
        'show' => 'stubkit::show.timestamp',
        'create' => 'stubkit::create.datetime',
        'edit' => 'stubkit::edit.datetime',
        'casts' => 'stubkit::casts.datetime',
    ],

    'file' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.file',
        'rules' => 'stubkit::rules.file',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.file',
        'edit' => 'stubkit::edit.file',
    ],

    'image' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.image',
        'rules' => 'stubkit::rules.image',
        'index' => 'stubkit::index.image',
        'show' => 'stubkit::show.image',
        'create' => 'stubkit::create.image',
        'edit' => 'stubkit::edit.image',
    ],

    'foreign' => [
        'schema' => 'stubkit::schema.foreign',
        'faker' => 'stubkit::faker.foreign',
        'rules' => 'stubkit::rules.foreign',
        'index' => 'stubkit::index.foreign',
        'show' => 'stubkit::show.foreign',
        'create' => 'stubkit::create.foreign',
        'edit' => 'stubkit::edit.foreign',
    ],

    'uuid' => [
        'schema' => 'stubkit::schema.uuid',
        'faker' => 'stubkit::faker.uuid',
        'rules' => 'stubkit::rules.none',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.none',
        'edit' => 'stubkit::edit.none',
    ],

    'id' => [
        'schema' => 'stubkit::schema.id',
        'faker' => 'stubkit::faker.none',
        'rules' => 'stubkit::rules.none',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.none',
        'edit' => 'stubkit::edit.none',
    ],

    'full_name' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.full_name',
        'rules' => 'stubkit::rules.string',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.text',
        'edit' => 'stubkit::edit.text',
    ],

    'first_name' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.first_name',
        'rules' => 'stubkit::rules.string',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.text',
        'edit' => 'stubkit::edit.text',
    ],

    'last_name' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.last_name',
        'rules' => 'stubkit::rules.string',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.text',
        'edit' => 'stubkit::edit.text',
    ],

    'username' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.username',
        'rules' => 'stubkit::rules.username',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.text',
        'edit' => 'stubkit::edit.text',
    ],

    'phone' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.phone',
        'rules' => 'stubkit::rules.text',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.tel',
        'edit' => 'stubkit::edit.tel',
    ],

    'email' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.email',
        'rules' => 'stubkit::rules.email',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.email',
        'edit' => 'stubkit::edit.email',
    ],

    'url' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.url',
        'rules' => 'stubkit::rules.url',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.text',
        'edit' => 'stubkit::edit.text',
    ],

    'ip' => [
        'schema' => 'stubkit::schema.ip',
        'faker' => 'stubkit::faker.ip',
        'rules' => 'stubkit::rules.ip',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.text',
        'edit' => 'stubkit::edit.text',
    ],

    'password' => [
        'schema' => 'stubkit::schema.string',
        'faker' => 'stubkit::faker.password',
        'rules' => 'stubkit::rules.password',
        'index' => 'stubkit::index.text',
        'show' => 'stubkit::show.text',
        'create' => 'stubkit::create.password',
        'edit' => 'stubkit::edit.password',
    ],
];
