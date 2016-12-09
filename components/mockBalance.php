<?php
    return $mockBalance = array(
        '64FA32000D' => array(
            '@attributes' => array
                (
                    'ver' => '3',
                    'direct' => '2',
                    'lang' => 'RU',
                ),
            'answer' => array
                (
                    'CLIENT' => array
                        (
                            '@attributes' => array
                                (
                                    'ID_CARD' => '4',
                                    'ID_CLIENT' => '6',
                                    'NAME' => 'Маслова Е.Н.',
                                )
                        ),
                    'ITEM' => array
                        (
                            '0' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '10.10.2016',
                                            'PURCHASE_SYMA' => '1800',
                                            'PURCHASE_QTY' => '8',
                                            'NAME' => 'Бас.Абонемент.Занянятие по плаванию (8-12 пос.)',
                                            'ID' => '4',
                                            'QTY' => '3',
                                            'PRICE' => '225',
                                            'UNIT' => '',
                                            'ACTIVE' => '1',
                                            'PAID' => '1',
                                        )
                                ),
                            '1' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '11.10.2016',
                                            'PURCHASE_SYMA' => '1800',
                                            'PURCHASE_QTY' => '8',
                                            'NAME' => 'Бас.Абонемент.Занянятие по плаванию (8-12 пос.)',
                                            'ID' => '4',
                                            'QTY' => '0',
                                            'PRICE' => '225',
                                            'UNIT' => '',
                                            'ACTIVE' => '0',
                                            'PAID' => '1',
                                        )
                                ),
                        ),
                ),
            'result' => array
                (
                    '@attributes' => array
                        (
                            'code' => '0',
                            'text' => 'OK',
                        )

                ),
        ),
        '92FC820003' => array
        (
            '@attributes' => array
                (
                    'ver' => '3',
                    'direct' => '2',
                    'lang' => 'RU',
                ),
            'answer' => array
                (
                    'CLIENT' => array
                        (
                            '@attributes' => array
                                (
                                    'ID_CARD' => '1',
                                    'ID_CLIENT' => '4',
                                    'NAME' => 'Ковалев И.Ю.',
                                )
                        ),
                    'ITEM' => array
                        (
                            '0' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '10.10.2016',
                                            'PURCHASE_SYMA' => '1800',
                                            'PURCHASE_QTY' => '8',
                                            'NAME' => 'Бас.Абонемент.Занянятие по плаванию (8-12 пос.)',
                                            'ID' => '4',
                                            'QTY' => '6',
                                            'PRICE' => '225',
                                            'UNIT' => '',
                                            'ACTIVE' => '0',
                                            'PAID' => '1',
                                        )
                                ),
                            '1' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '10.10.2016',
                                            'PURCHASE_SYMA' => '100',
                                            'PURCHASE_QTY' => '1',
                                            'PURCHASE_FINISH' => '10.11.2016',
                                            'NAME' => 'Бас.Разовое.Занянятие по плаванию (сотрудники)',
                                            'ID' => '40',
                                            'QTY' => '0',
                                            'PRICE' => '100',
                                            'UNIT' => '',
                                            'ACTIVE' => '0',
                                            'PAID' => '1',
                                        )
                                ),
                            '2' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '10.10.2016',
                                            'PURCHASE_SYMA' => '1000',
                                            'PURCHASE_QTY' => '1',
                                            'PURCHASE_FINISH' => '10.11.2016',
                                            'NAME' => 'Бас.Абонемент.Занянятие по плаванию (сотрудники) (12 занятий)',
                                            'ID' => '41',
                                            'QTY' => '12',
                                            'PRICE' => '1000',
                                            'UNIT' => '',
                                            'ACTIVE' => '1',
                                            'PAID' => '1',
                                        )
                                )
                        )
                ),
            'result' => array
                (
                    '@attributes' => array
                        (
                            'code' => '0',
                            'text' => 'OK',
                        )
                )
        ),
        // корпоративная
        '179AFF0029' =>
        array(
            '@attributes' => array
                (
                    'ver' => '3',
                    'direct' => '2',
                    'lang' => 'RU'
                ),
            'answer' => array
                (
                    'CLIENT' => array
                        (
                            '@attributes' => array
                                (
                                    'ID_CARD' => '2003',
                                    'ID_CLIENT' => '14247',
                                    'NAME' => 'Каф-ра спортивных игр',
                                ),
                        ),
                    'ITEM' => array
                        (
                            '@attributes' => array
                                (
                                    'PURCHASE_DATE' => '26.12.2015',
                                    'PURCHASE_SYMA' => '0',
                                    'PURCHASE_QTY' => '30',
                                    'NAME' => 'ГЗ. СибГУФК. Занятия учебных групп',
                                    'ID' => '117',
                                    'QTY' => '0',
                                    'PRICE' => '0',
                                    'UNIT' => '',
                                    'ACTIVE' => '0',
                                    'PAID' => '1',
                                ),
                        ),
                ),
            'result' => array
                (
                    '@attributes' => array
                        (
                            'code' => '0',
                            'text' => 'OK'
                        ),
                ),
        ),
        // корпоративная с 2-мя одинаковыми услугами
        '5714270030' =>
        array
        (
            '@attributes' => array
                (
                    'ver' => '3',
                    'direct' => '2',
                    'lang' => 'RU'
                ),
            'answer' => array
                (
                    'CLIENT' => array
                        (
                            '@attributes' => array
                                (
                                    'ID_CARD' => '12337',
                                    'ID_CLIENT' => '4880',
                                    'NAME' => 'ОНИИП (открытый бассейн)',
                                ),
                        ),
                    'ITEM' => array
                        (
                            '0' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '07.11.2014',
                                            'PURCHASE_SYMA' => '0',
                                            'PURCHASE_QTY' => '40',
                                            'NAME' => 'ГЗ. ОНИИП (Откр. бассейн)',
                                            'ID' => '158',
                                            'QTY' => '12',
                                            'PRICE' => '0',
                                            'UNIT' => 'сеанс',
                                            'ACTIVE' => '1',
                                            'PAID' => '1',
                                        ),
                                ),
                            '1' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '01.11.2016',
                                            'PURCHASE_SYMA' => '0',
                                            'PURCHASE_QTY' => '80',
                                            'NAME' => 'ГЗ. ОНИИП (Откр. бассейн)',
                                            'ID' => '158',
                                            'QTY' => '80',
                                            'PRICE' => '0',
                                            'UNIT' => 'сеанс',
                                            'ACTIVE' => '1',
                                            'PAID' => '1',
                                        ),
                                ),
                        ),
                ),
            'result' => array
                (
                    '@attributes' => array
                        (
                            'code' => '0',
                            'text' => 'OK'
                        ),
                ),
        ),
        // корпоративная без услуг
        'C985FF0029' =>
        array
        (
            '@attributes' => array
                (
                    'ver' => '3',
                    'direct' => '2',
                    'lang' => 'RU'
                ),
            'answer' => array
                (
                    'CLIENT' => array
                        (
                            '@attributes' => array
                                (
                                    'ID_CARD' => '20',
                                    'ID_CLIENT' => '10314',
                                    'NAME' => 'Высшая школа тренеров ОБ',
                                ),
                        ),
                ),
            'result' => array
                (
                    '@attributes' => array
                        (
                            'code' => '0',
                            'text' => 'OK'
                        ),
                ),
        ),
        // реальный клиент
        '256702006A' => array
            (
                '@attributes' => array
                    (
                        'ver' => '3',
                        'direct' => '2',
                        'lang' => 'RU'
                    ),
                'answer' => array
                    (
                        'CLIENT' => array
                            (
                                '@attributes' => array
                                    (
                                        'ID_CARD' => '14027',
                                        'ID_CLIENT' => '13249',
                                        'NAME' => 'Красильникова Е.Ю.',
                                    ),
                            ),
                        'ITEM' => array
                            (
                                '0' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '20.01.2015',
                                                'PURCHASE_SYMA' => '800',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '15.03.2015',
                                                'NAME' => 'КБ. Взрослые. Абонемент (мин 4 занятия)',
                                                'ID' => '9',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '1' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '18.03.2015',
                                                'PURCHASE_SYMA' => '800',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '18.04.2015',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '2' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '02.04.2015',
                                                'PURCHASE_SYMA' => '800',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '25.05.2015',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '3' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '21.05.2015',
                                                'PURCHASE_SYMA' => '800',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '21.06.2015',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '4' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '04.06.2015',
                                                'PURCHASE_SYMA' => '800',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '04.07.2015',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '5' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '30.06.2015',
                                                'PURCHASE_SYMA' => '800',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '27.09.2015',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '1',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '1',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '6' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '27.10.2015',
                                                'PURCHASE_SYMA' => '960',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '27.11.2015',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '7' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '10.11.2015',
                                                'PURCHASE_SYMA' => '960',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '10.12.2015',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '8' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '01.12.2015',
                                                'PURCHASE_SYMA' => '960',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '01.01.2016',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '9' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '15.12.2015',
                                                'PURCHASE_SYMA' => '960',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '15.01.2016',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '10' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '28.01.2016',
                                                'PURCHASE_SYMA' => '1000',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '28.02.2016',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '11' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '10.03.2016',
                                                'PURCHASE_SYMA' => '1000',
                                                'PURCHASE_QTY' => '4',
                                                'PURCHASE_FINISH' => '10.04.2016',
                                                'NAME' => 'ОБ. Взрослые. Абонемент (от 4 занятий в месяц)',
                                                'ID' => '66',
                                                'QTY' => '0',
                                                'PRICE' => '250',
                                                'UNIT' => 'сеанс',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '12' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '14.04.2016',
                                                'PURCHASE_SYMA' => '300',
                                                'PURCHASE_QTY' => '1',
                                                'PURCHASE_FINISH' => '14.05.2016',
                                                'NAME' => 'ОБ. 100 минут.',
                                                'ID' => '429',
                                                'QTY' => '-1',
                                                'PRICE' => '300',
                                                'UNIT' => '',
                                                'ACTIVE' => '0',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '13' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '21.04.2016',
                                                'PURCHASE_SYMA' => '300',
                                                'PURCHASE_QTY' => '1',
                                                'PURCHASE_FINISH' => '21.05.2016',
                                                'NAME' => 'ОБ. 100 минут.',
                                                'ID' => '429',
                                                'QTY' => '0',
                                                'PRICE' => '300',
                                                'UNIT' => '',
                                                'ACTIVE' => '1',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '14' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '19.05.2016',
                                                'PURCHASE_SYMA' => '1250',
                                                'PURCHASE_QTY' => '1',
                                                'PURCHASE_FINISH' => '15.07.2016',
                                                'NAME' => 'ОБ. 500 минут.',
                                                'ID' => '430',
                                                'QTY' => '0',
                                                'PRICE' => '1250',
                                                'UNIT' => '',
                                                'ACTIVE' => '1',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '15' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '07.07.2016',
                                                'PURCHASE_SYMA' => '1250',
                                                'PURCHASE_QTY' => '1',
                                                'PURCHASE_FINISH' => '21.08.2016',
                                                'NAME' => 'ОБ. 500 минут.',
                                                'ID' => '430',
                                                'QTY' => '17',
                                                'PRICE' => '1250',
                                                'UNIT' => '',
                                                'ACTIVE' => '1',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '16' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '23.08.2016',
                                                'PURCHASE_SYMA' => '1250',
                                                'PURCHASE_QTY' => '1',
                                                'PURCHASE_FINISH' => '24.09.2016',
                                                'NAME' => 'ОБ. 500 минут.',
                                                'ID' => '430',
                                                'QTY' => '38',
                                                'PRICE' => '1250',
                                                'UNIT' => '',
                                                'ACTIVE' => '1',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '17' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '06.10.2016',
                                                'PURCHASE_SYMA' => '300',
                                                'PURCHASE_QTY' => '1',
                                                'PURCHASE_FINISH' => '07.11.2016',
                                                'NAME' => 'ОБ. 100 минут.',
                                                'ID' => '429',
                                                'QTY' => '0',
                                                'PRICE' => '300',
                                                'UNIT' => '',
                                                'ACTIVE' => '1',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '18' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '27.10.2016',
                                                'PURCHASE_SYMA' => '1250',
                                                'PURCHASE_QTY' => '1',
                                                'PURCHASE_FINISH' => '30.12.2016',
                                                'NAME' => 'ОБ. 500 минут.',
                                                'ID' => '430',
                                                'QTY' => '0',
                                                'PRICE' => '1250',
                                                'UNIT' => '',
                                                'ACTIVE' => '1',
                                                'PAID' => '1',
                                            ),
                                    ),
                                '19' => array
                                    (
                                        '@attributes' => array
                                            (
                                                'PURCHASE_DATE' => '08.12.2016',
                                                'PURCHASE_SYMA' => '1250',
                                                'PURCHASE_QTY' => '1',
                                                'PURCHASE_FINISH' => '09.01.2017',
                                                'NAME' => 'ОБ. 500 минут.',
                                                'ID' => '430',
                                                'QTY' => '500',
                                                'PRICE' => '1250',
                                                'UNIT' => '',
                                                'ACTIVE' => '1',
                                                'PAID' => '1',
                                            ),
                                    ),
                            ),
                    ),
                'result' => array
                    (
                        '@attributes' => array
                            (
                                'code' => '0',
                                'text' => 'OK'
                            ),
                    ),
            ),
        // клиент с долгами
        '4F97670088' =>
        array
        (
            '@attributes' => array
                (
                    'ver' => '3',
                    'direct' => '2',
                    'lang' => 'RU'
                ),
            'answer' => array
                (
                    'CLIENT' => array
                        (
                            '@attributes' => array
                                (
                                    'ID_CARD' => '26963',
                                    'ID_CLIENT' => '25313',
                                    'NAME' => 'Иванов',
                                ),
                        ),
                    'ITEM' => array
                        (
                            '0' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '08.12.2016',
                                            'PURCHASE_SYMA' => '0',
                                            'PURCHASE_QTY' => '4',
                                            'PURCHASE_FINISH' => '09.01.2017',
                                            'NAME' => 'ОБ. Дети. 100 минут.',
                                            'ID' => '433',
                                            'QTY' => '370',
                                            'PRICE' => '200',
                                            'UNIT' => '',
                                            'ACTIVE' => '1',
                                            'PAID' => '1',
                                        ),
                                ),
                            '1' => array
                                (
                                    '@attributes' => array
                                        (
                                            'PURCHASE_DATE' => '08.12.2016',
                                            'PURCHASE_SYMA' => '800',
                                            'PURCHASE_QTY' => '4',
                                            'NAME' => 'ОБ. Дети. 100 минут.',
                                            'ID' => '433',
                                            'QTY' => '0',
                                            'PRICE' => '200',
                                            'UNIT' => '',
                                            'ACTIVE' => '0',
                                            'PAID' => '0',
                                        ),
                                ),
                        ),
                ),
            'result' => array
                (
                    '@attributes' => array
                        (
                            'code' => '0',
                            'text' => 'OK'
                        ),
                ),
        )
    );
