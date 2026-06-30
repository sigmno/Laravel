<?php

return [
    'accepted' => 'Поле :attribute должно быть принято.',
    'confirmed' => 'Подтверждение поля :attribute не совпадает.',
    'current_password' => 'Текущий пароль указан неверно.',
    'email' => 'Поле :attribute должно быть корректным email.',
    'exists' => 'Выбранное значение поля :attribute некорректно.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'lowercase' => 'Поле :attribute должно быть в нижнем регистре.',
    'lte' => 'Поле :attribute должно быть меньше или равно :value.',
    'max' => [
        'numeric' => 'Поле :attribute не должно быть больше :max.',
        'string' => 'Поле :attribute не должно быть длиннее :max символов.',
    ],
    'min' => [
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
        'string' => 'Поле :attribute должно быть не короче :min символов.',
    ],
    'required' => 'Поле :attribute обязательно для заполнения.',
    'string' => 'Поле :attribute должно быть строкой.',
    'unique' => 'Такое значение поля :attribute уже используется.',

    'attributes' => [
        'name' => 'имя',
        'email' => 'email',
        'password' => 'пароль',
        'current_password' => 'текущий пароль',
        'title' => 'название',
        'author_id' => 'автор',
        'genre_id' => 'жанр',
        'year' => 'год',
        'isbn' => 'ISBN',
        'total_copies' => 'всего экземпляров',
        'available_copies' => 'доступно экземпляров',
        'status' => 'статус',
        'comment' => 'комментарий',
    ],
];
