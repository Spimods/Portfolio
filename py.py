données = [
    ['jour', 'mois', 'années'],
    ['01', '04', '2024'],
    ['23', '11', '2026']
]
td = ""
for i in range(len(données)):
    td += "    <tr>"
    for a in range(len(données[i])):
        td += "<td>" + str(données[i][a]) + "</td>"
    td += "</tr>\n"

html_content = f"""
<!DOCTYPE html>
<html>
    <body>
<table>

{td}
</table>
    </body>
</html>
"""
fichier = open("test.html", "w")
fichier.write(html_content)
