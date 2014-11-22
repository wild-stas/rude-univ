<?

namespace rude;

class disciplines
{
	public static function get($id = null)
	{
		$q = new query(RUDE_DATABASE_TABLE_DISCIPLINES);

		if ($id !== null)
		{
			$q->where(RUDE_DATABASE_FIELD_ID, (int) $id);
			$q->query();

			return $q->get_object();
		}

		$q->query();

		return $q->get_object_list();
	}

	public static function remove($id)
	{
		if (static::is_exists($id))
		{
			$q = new dquery(RUDE_DATABASE_TABLE_DISCIPLINES);
			$q->where(RUDE_DATABASE_FIELD_ID, (int) $id);
			$q->query();

			return $q->affected();
		}

		return false;
	}

	public static function is_exists($id)
	{
		if (static::get($id))
		{
			return true;
		}

		return false;
	}

	public static function add($name)
	{
		$q = new cquery(RUDE_DATABASE_TABLE_DISCIPLINES);
		$q->add(RUDE_DATABASE_FIELD_NAME, $name);
		$q->query();

		return true;
	}
}