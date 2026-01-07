# easyway_yt

pandoc sample.docx -t json -o sample.json
pandoc sample.docx -s -o sample.html --mathjax
pandoc sample.docx -t plain -o questions.txt
pandoc sample.docx -t json -o questions.json
pandoc sample.docx -o sample.tex


pandoc input.docx -t plain -o output.txt --mathjax --extract-media=/var/www/myproject/easyway_yt/public/word_files
pandoc /full/path/to/your/input.docx -t plain -o output.txt --mathjax --extract-media=/var/www/myproject/easyway_yt/public/word_files




pandoc ./public/word_files/sample.docx  -t plain -o ./public/word_files/output.txt --mathjax --extract-media=/var/www/myproject/easyway_yt/public/word_files/


pandoc ./public/word_files/sample.docx \
  -t html \         
  --mathjax \
  --extract-media=./public/word_files/media
  https://chatgpt.com/c/685a8fb4-74f4-8010-87ce-564952c69ced
