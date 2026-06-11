public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'date' => $this->date,
        'level' => $this->level,
        'score' => $this->score,
        'mood' => $this->mood,
        'note' => $this->note,

        'categories' => CategoryResource::collection(
            $this->whenLoaded('categories')
        ),

        'triggers' => $this->whenLoaded('triggers', function () {
            return $this->triggers->map(function ($trigger) {
                return [
                    'id' => $trigger->id,
                    'name' => $trigger->name,
                ];
            });
        }),

        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}
