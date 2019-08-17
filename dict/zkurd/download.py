# Derived from `dict/zkurd.py`
import requests
import string
from bs4 import BeautifulSoup
import re

# generating letter links
letters = 'http://zkurd.org/proje/ferhengdev.pl?letter='
generate_links = []
for generate_link in string.ascii_uppercase:
    generate_links.append(letters + str(generate_link))   

# Parsing each specified letter link
a = requests.get('http://zkurd.org/proje/ferhengdev.pl?letter=A')
soup = BeautifulSoup(a.text, 'lxml')

#Parsing inside pages related to the each letter
extra_pages = []
for extra_page in soup.find_all('a', href=re.compile("&dahatu=belle&tenha=")):
    extra_pages.append(extra_page['href'])

# Parsing the Entry   
get_entry = []
for table in soup.find_all('td', align="left"):
    get_entry.append(table.a.text)

# Parsing the Meaning
get_meaning = []
for meaning in soup.find_all('span'):
    get_meaning.append(meaning.a.text)

# Parsing the whole data and save it into a file names: zkurd.txt
for each_letter_link in generate_links:
##    print(each_letter_link)
    letter_link = requests.get(each_letter_link)
    soup = BeautifulSoup(letter_link.text, 'lxml')
    extra_pages = []
    for extra_page in soup.find_all('a', href=re.compile("&dahatu=belle&tenha=")):
        extra_pages.append('http://zkurd.org/proje/' + extra_page['href'])
    for each_page_link in extra_pages:
        url = requests.get(each_page_link)
        each_soup = BeautifulSoup(url.text, 'lxml')
        
        get_entry = []
        for table in each_soup.find_all('td', align="left"):
            get_entry.append(table.a.text)
        get_meaning = []
        for meaning in each_soup.find_all('span'):
            get_meaning.append(meaning.a.text)

        zip_dict = list(zip(get_entry, get_meaning))
        for entry, meaning in zip_dict:
            with open('zkurd.txt', 'a', encoding = 'utf-8') as f:
                f.write('\n'.join(['%s\t%s\n' % (entry, meaning)]))
                # Below writing function is specified to the mdx dictionary file.
                #f.write('\n'.join(['%s\n<link rel="stylesheet" href="zkurd.css" /><br /><div class="entry">%s</div><br /><div class="meaning">%s</div>\n</>\n' % (entry, entry, meaning)]))
