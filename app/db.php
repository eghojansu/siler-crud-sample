<?php

namespace App\db;

use App;
use Siler\Container;

/**
 * Create/get PDO instance
 *
 * @return PDO
 */
function pdo()
{
    if ($pdo = Container\get('pdo')) {
        return $pdo;
    }

    $cfg = App\env() + [
        'DB_HOST' => '',
        'DB_NAME' => '',
        'DB_PORT' => 3306,
        'DB_USER' => '',
        'DB_PASS' => null,
    ];
    try {
        $pdo = new \PDO(
            sprintf(
                'mysql:host=%s;dbname=%s;port=%s',
                $cfg['DB_HOST'],
                $cfg['DB_NAME'],
                $cfg['DB_PORT']
            ),
            $cfg['DB_USER'],
            $cfg['DB_PASS']
        );
        Container\set('pdo', $pdo);
    } catch (\Exception $e) {
        if (App\env('DEBUG')) {
            user_error($e->getMessage(), E_USER_ERROR);
        }

        user_error('Invalid database configuration', E_USER_ERROR);
    }

    return $pdo;
}

/**
 * Build filter, valid format string or [string, ...params]
 *
 * @param  string|array $filter
 * @return array
 */
function filter($filter)
{
    $f = [];
    if ($filter) {
        $f['params'] = [];
        $f['mode'] = '?';
        if (is_string($filter)) {
            $f['filter'] = $filter;
        } elseif (is_array($filter)) {
            $f['filter'] = array_shift($filter);
            if (false === strpos($f['filter'], ':')) {
                $f['params'] = array_values($filter);
            } else {
                $f['mode'] = ':';
                foreach ($filter as $key => $value) {
                    $f['params'][":$key"] = $value;
                }
            }
        }
    }

    return $f;
}

/**
 * Find in table
 *
 * @param  string       $table
 * @param  string|array $filter @see filter
 * @param  array        $opt
 * @return array        Record list
 */
function find($table, $filter = '', array $opt = [])
{
    $opt += [
        'column' => '*',
        'group'  => null,
        'having' => null,
        'order'  => null,
        'limit'  => 0,
        'offset' => 0,
        'params' => [],
        'fetch'  => \PDO::FETCH_ASSOC,
    ];

    $sql = 'SELECT '.$opt['column'].' FROM '.$table;
    if ($f = filter($filter)) {
        $sql .= ' WHERE '.$f['filter'];
        $opt['params'] = array_merge($opt['params'], $f['params']);
    }
    if ($opt['group']) {
        $sql .= ' GROUP BY '.$opt['group'];
    }
    if ($f = filter($opt['having'])) {
        $sql .= ' HAVING '.$f['filter'];
        $opt['params'] = array_merge($opt['params'], $f['params']);
    }
    if ($opt['order']) {
        $sql .= ' ORDER BY '.$opt['order'];
    }
    if ($opt['limit']) {
        $sql .= ' LIMIT '.$opt['limit'];
    }
    if ($opt['offset']) {
        $sql .= ' OFFSET '.$opt['offset'];
    }

    $stmt = pdo()->prepare($sql);
    $stmt->execute($opt['params']);

    $error = $stmt->errorInfo();
    if ('00000' !== $error[0]) {
        user_error($error[2] . " ($sql)", E_USER_ERROR);
    }

    return $stmt->fetchAll($opt['fetch']) ?: [];
}

/**
 * Find one in table
 *
 * @param  string        $table
 * @param  string|array  $filter @see filter
 * @param  array         $opt @see find
 * @return array|null    if no result
 */
function findone($table, $filter = '', array $opt = [])
{
    $records = find($table, $filter, ['limit'=>1] + $opt);

    return $records ? array_pop($records) : [];
}

/**
 * Create record in table
 *
 * @param  string $table
 * @param  array  $data
 * @return int last inserted id
 */
function insert($table, array $data)
{
    $sql = 'INSERT INTO '.$table.
        ' ('.implode(', ', array_keys($data)).')'.
        ' VALUES'.
        ' ('.str_repeat('?, ', count($data)-1).'?)';
    $params = array_values($data);

    $pdo  = pdo();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $error = $stmt->errorInfo();
    $result = '00000' === $error[0];

    if (!$result) {
        user_error($error[2] . " ($sql)", E_USER_ERROR);
    }

    return $pdo->lastInsertId();
}

/**
 * Insert batch
 *
 * @param  string $table
 * @param  array  $data
 * @param  array  $placeholder
 * @return array
 */
function insert_batch($table, array $data, array $placeholder)
{
    $sql = 'INSERT INTO '.$table.
        ' ('.implode(', ', array_keys($placeholder)).')'.
        ' VALUES'.
        ' ('.str_repeat('?, ', count($placeholder)-1).'?)';

    $pdo  = pdo();
    $stmt = $pdo->prepare($sql);

    $results = [];
    foreach ($data as $key => $value) {
        $params = [];

        foreach ($placeholder as $pkey => $pvalue) {
            if (is_null($pvalue)) {
                $params[] = $value;
            } elseif (is_callable($pvalue)) {
                $params[] = call_user_func_array($pvalue, [$value, $key, $data]);
            } else {
                $params[] = $pvalue;
            }

        }

        $stmt->execute($params);
        $result[] = $pdo->lastInsertId();
    }

    return $result;
}

/**
 * Update record
 *
 * @param  string       $table
 * @param  array        $data
 * @param  string|array $filter @see filter
 * @param  array        $params Extra data
 * @return boolean
 */
function update($table, array $data, $filter = '', array $params = [])
{
    $f = filter($filter);
    $values = '';
    $named_slot = $f && ':' === $f['mode'];
    foreach ($data as $key => $value) {
        $values .= ($values ? ',' : '') . $key . ' = ';
        if (is_array($value)) {
            // expression
            $values .= array_shift($value);
        } else {
            if ($named_slot) {
                $values .= ":$key";
                $params[":$key"] = $value;
            } else {
                $values .= "?";
                $params[] = $value;
            }
        }
    }
    $sql = 'UPDATE '.$table.' SET '.$values;
    if ($f) {
        $sql .= ' WHERE '.$f['filter'];
        $params = array_merge($params, $f['params']);
    }

    $stmt = pdo()->prepare($sql);
    $stmt->execute($params);

    $error = $stmt->errorInfo();
    $result = '00000' === $error[0];

    if (!$result) {
        user_error($error[2] . " ($sql)", E_USER_ERROR);
    }

    return $result;
}

/**
 * Remove record
 *
 * @param  string       $table
 * @param  string|array $filter
 * @return boolean
 */
function delete($table, $filter = '')
{
    $f = filter($filter);
    $sql = 'DELETE FROM '.$table;
    if ($f) {
        $sql .= ' WHERE '.$f['filter'];
    }

    $stmt = pdo()->prepare($sql);
    $stmt->execute($f['params'] ?? []);

    $error = $stmt->errorInfo();
    $result = '00000' === $error[0];

    if (!$result) {
        user_error($error[2] . " ($sql)", E_USER_ERROR);
    }

    return $result;
}

/**
 * Get latest post
 *
 * @param  string $slug
 * @param  string $category
 * @return array
 */
function find_post($slug = null, $category = null)
{
    $tbl  = 'view_post';
    $cat_filter = '';
    $params = [];
    if ($category) {
        $cat_filter = 'categories like ?';
        $params[] = '%'.$category.'%';
    }

    $filter = $cat_filter . ($slug ? ($cat_filter ? ' and ' : '') . 'slug = ?' : '');
    $post = findone(
        $tbl,
        array_filter(array_merge([$filter], $params, [$slug])),
        ['order'=>'created_at desc, id desc']
    );
    $prev = null;
    $next = null;
    if ($post) {
        update('post', ['hit_counter' => ['hit_counter + 1']], ['id = ?', $post['id']]);

        $filter = $cat_filter . ($cat_filter ? ' and ' : '');
        $prev = findone(
            $tbl,
            array_merge([$filter.'id < ?'], $params, [$post['id']]),
            ['order'=>'id desc']
        );
        $next = findone(
            $tbl,
            array_merge([$filter.'id > ?'], $params, [$post['id']]),
            ['order'=>'id asc']
        );
    }

    return [
        'post' => $post,
        'prev' => $prev,
        'next' => $next,
    ];
}
