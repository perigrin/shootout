/* The Great Win32 Computer Language Shootout 
   http://dada.perl.it/shootout/

   contributed by Isaac Gouy (Clean novice)
*/


module harmonic
import StdEnv, LanguageShootout

Start = toStringWith 9 (loop argi 0.0 0.0)

loop :: !Int !Real !Real -> Real
loop i d sum 
  | i > 0       = loop (i-1) d` (s + (1.0/d`)) 
  | otherwise   = sum
  where d` = d + 1.0