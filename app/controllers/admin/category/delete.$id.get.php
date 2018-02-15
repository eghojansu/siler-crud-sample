<?php

namespace App;

use Siler\Http;

db\delete('category', ['id = ?', $params['id']]);

Http\setsession('action_message', 'Category has been deleted');
redirect('admin/category');
