import re

path = r'C:\Kuliah\SEMESTER 6\Data Science Programming\medical_AI\Laravel_Dashboard\resources\views\analysis\result.blade.php'

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

def replace_with_array_check(field_name):
    old_str = f"{{{{ $result['{field_name}'] }}}}"
    new_str = f"""@if(is_array($result['{field_name}']))
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($result['{field_name}'] as $item)
                                <li>{{{{ $item }}}}</li>
                            @endforeach
                        </ul>
                    @else
                        {{{{ $result['{field_name}'] }}}}
                    @endif"""
    return old_str, new_str

fields = ['confidence_reason', 'treatment_information', 'potential_complications']

for field in fields:
    old, new = replace_with_array_check(field)
    content = content.replace(old, new)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
