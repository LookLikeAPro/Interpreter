<?php
$input = '3+4+5';
echo evaluate(tree($input));
function isF($char)
{
  if ($char=='+' || $char=='-' || $char=='*' || $char=='/' || $char=='^')
    return true;
  else
    return false;
}
function unbracket($term)
{
  if (substr($term,0,1) == '(' && substr($term,strlen($term)-1,strlen($term))==')')
    return unbracket(substr($term, 1, strlen($term)-2));
  else return $term;
}

function tree($term)
{
  $term = unbracket($term);
  $bracket = 0;
  $expressions = array();
  for($i="0"; $i<strlen($term); $i=$i+1)
  {
    $char = substr($term,$i,1);
    if ($char=='(')
    {
      $bracket++;
    }
    else if ($char==')')
    {
      $bracket--;
    }
    else if (isF($char) && $bracket==0)
    {
      $expressions[] = array($i, $char);
    }
  }
  foreach($expressions as $expression)
  {
    if ($expression[1]=='+' || $expression[1]=='-')
      return array(tree(substr($term,0,$expression[0])), substr($term,$expression[0],1), tree(substr($term,$expression[0]+1,strlen($term))));
  }
  foreach($expressions as $expression)
  {
    if ($expression[1]=='/' || $expression[1]=='*')
      return array(tree(substr($term,0,$expression[0])), substr($term,$expression[0],1), tree(substr($term,$expression[0]+1,strlen($term))));
  }
  foreach($expressions as $expression)
  {
    if ($expression[1]=='^')
      return array(tree(substr($term,0,$expression[0])), substr($term,$expression[0],1), tree(substr($term,$expression[0]+1,strlen($term))));
  }
  return $term;
}

function untree($term)
{
  if (count($term)==1)
    return $term;
  else
  {
    return '('.untree($term[0]).$term[1].untree($term[2]).')';
  }
}

function evaluate($term)
{
  if (is_array($term))
  {
    switch ($term[1]){
      case '+':
      return evaluate($term[0])+evaluate($term[2]);
      case '-':
      return evaluate($term[0])-evaluate($term[2]);
      case '*':
      return evaluate($term[0])*evaluate($term[2]);
      case '/':
      return evaluate($term[0])/evaluate($term[2]);
      case '^':
      {	$acc = 1;
       $base = evaluate($term[0]);
       for ($i=0;$i<evaluate($term[2]);$i++)
         $acc *= $base;
       return $acc;
      }
    }
  }
  else
  {
    return $term;
  }
}
// TODO - Simplify - evaluate while accounting for unknown variables
function simplify($term)
{
  if (count($term)==1)
    return $term;
  else
  {
    switch ($term[1]){
      case '+':
      {
        $left = simplify($term[0]);
        $right = simplify($term[2]);
        if (is_real($left) && is_real($right))
          return $left+$right;
        else if (is_array($right))
        {
          for ($i=0; $i<(count($right)+1)/2; $i++)
          {
            $try = simplify(array($left, $term[1], $right[$i*2]));
            if (count($try)==1)
            {
              $right[$i*2] = $try;
              return $right;
            }
          }
        }
        else
        {
          return array($left,$term[1],$right);
        }
      }
      case '-':
      return evaluate($term[0])-evaluate($term[2]);
      case '*':
      return evaluate($term[0])*evaluate($term[2]);
      case '/':
      return evaluate($term[0])/evaluate($term[2]);
      case '^':
      {	$acc = 1;
       $base = evaluate($term[0]);
       for ($i=0;$i<evaluate($term[2]);$i++)
         $acc *= $base;
       return $acc;
      }
    }
  }
}

?>
