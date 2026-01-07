-- resources/pandoc/image_math.lua

-- Replace images with a placeholder containing the extracted filename
function Image(img)
  -- Use full path (adjust if you want relative instead)
--   local full = pandoc.path.normalize(img.src)
  return pandoc.Str("[[IMAGE:" .. img.src .. "]]")
end

-- Replace math with a placeholder containing the TeX source
-- function Math(m)
--   return pandoc.Str("$$" .. m.text .. "$$")
-- end
