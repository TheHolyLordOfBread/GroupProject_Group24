
from datetime import datetime
from pyscript import web, when

now = datetime.now()

print(f"Hello from Python! Current time: {now}")

output_div = web.page["results"]

print(f"hello {output_div}")

output_div.innerText = now